<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('task_id') && !ctype_digit($request->query('task_id'))) {
            return redirect('/tasks')
                ->withErrors(['task_search' => 'Task ID must be a positive number'])
                ->withInput($request->only(['task_id', 'status', 'per_page']))
                ->with('open_modal', 'search');
        }

        $perPage = (int) $request->query('per_page', 20);
        $perPage = in_array($perPage, [20, 50, 100], true) ? $perPage : 20;

        $statusFilter = (array) $request->query('status', []);
        $statusFilter = array_filter($statusFilter, fn ($val) => $val !== '' && $val !== null);
        $taskIdFilter = $request->query('task_id');

        $query = Task::withExists('logs');

        if (!empty($statusFilter)) {
            $query->whereIn('status', $statusFilter);
        }

        if (!empty($taskIdFilter)) {
            $query->where('id', (int) $taskIdFilter);
        }

        $tasks = $query->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        $statuses = Task::select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return view('tasks.list', [
            'tasks' => $tasks,
            'statuses' => $statuses,
            'statusFilter' => $statusFilter,
            'perPage' => $perPage,
            'taskIdFilter' => $taskIdFilter,
        ]);
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registrar' => 'required|string|max:255',
            'country'   => 'required|string|max:255',
            'brand'     => 'nullable|string|max:255',
            'domains'   => 'required|string',
        ]);

        if ($validator->fails()) {
            $redirectTo = $request->input('form_context') === 'inline'
                ? '/tasks'
                : url()->previous();

            return redirect($redirectTo)
                ->withErrors($validator)
                ->withInput()
                ->with('open_modal', $request->input('form_context') === 'inline' ? 'task' : null);
        }

        // Преобразуем домены в массив
        $domains = preg_split('/\r\n|\r|\n/', trim($request->domains));
        $domains = array_filter($domains); // убираем пустые строки

        $created = 0;

        foreach ($domains as $domain) {

            Task::create([
                'registrar' => $request->registrar,
                'country'   => $request->country,
                'brand'     => $request->brand,
                'domain'    => trim($domain),
                'status'    => 'created',
                'completed' => false,
            ]);

            $created++;
        }

        return redirect('/tasks')->with('success', "Created {$created} tasks.");
    }

    public function batchStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'batch_registrar' => 'required|string|max:255',
            'batch_file'      => 'required|file|mimes:xlsx,csv,txt',
        ]);

        if ($validator->fails()) {
            return redirect('/tasks')
                ->withErrors($validator)
                ->with('open_modal', 'batch');
        }

        try {
            $rows = $this->extractRowsFromUpload($request->file('batch_file'));
        } catch (ValidationException $e) {
            return redirect('/tasks')
                ->withErrors($e->errors())
                ->with('open_modal', 'batch');
        }

        if (empty($rows)) {
            return redirect('/tasks')
                ->with('open_modal', 'batch')
                ->with('error', 'Uploaded file has no data.');
        }

        $created = 0;
        $skipped = 0;
        $registrar = $request->input('batch_registrar');

        foreach ($rows as $index => $row) {
            $domain = trim($row[0] ?? '');
            $brand = trim($row[1] ?? '');
            $country = strtolower(trim($row[2] ?? ''));

            if ($index === 0 && strcasecmp($domain, 'domain') === 0) {
                continue;
            }

            if ($domain === '' || $country === '') {
                $skipped++;
                continue;
            }

            Task::create([
                'registrar' => $registrar,
                'country'   => $country,
                'brand'     => $brand ?: null,
                'domain'    => $domain,
                'status'    => 'created',
                'completed' => false,
            ]);

            $created++;
        }

        if ($created === 0) {
            return redirect('/tasks')
                ->with('open_modal', 'batch')
                ->with('error', 'No valid rows were found in the file.');
        }

        $message = "Imported {$created} tasks.";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} row(s) due to missing data.";
        }

        return redirect('/tasks')->with('success', $message);
    }

    public function delete($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return redirect('/tasks')->with('error', 'Task not found');
        }

        $task->delete();

        return redirect('/tasks')->with('success', 'Task deleted successfully');
    }

    public function logs(Task $task)
    {
        $logs = Log::where('task_id', $task->id)
            ->orderBy('created_at', 'desc')
            ->get(['created_at', 'type', 'template_name', 'text', 'error_id']);

        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'logs' => $logs,
        ]);
    }

    public function account(Task $task)
    {
        $fields = [
            'registrar_email'    => 'Registrar Email',
            'registrar_login'    => 'Registrar Login',
            'registrar_password' => 'Registrar Password',
            'email_password'     => 'Email Password',
            'first_name'         => 'First Name',
            'last_name'          => 'Last Name',
            'city'               => 'City',
            'address'            => 'Address',
            'zip'                => 'ZIP',
            'phone'              => 'Phone',
            'proxy'              => 'Proxy',
            'security_qa'        => 'Security QA',
        ];

        $account = [];
        foreach ($fields as $field => $label) {
            $account[] = [
                'label' => $label,
                'value' => $task->{$field},
            ];
        }

        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'account' => $account,
        ]);
    }

    private function extractRowsFromUpload(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'csv' || $extension === 'txt') {
            return $this->parseCsv($file->getRealPath());
        }

        return $this->parseXlsx($file->getRealPath());
    }

    private function parseCsv(string $path): array
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        return $rows;
    }

    private function parseXlsx(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages([
                'batch_file' => 'Unable to open Excel file.',
            ]);
        }

        $sharedStrings = [];
        $sharedContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedContent !== false) {
            $xml = simplexml_load_string($sharedContent, 'SimpleXMLElement', LIBXML_NOCDATA);
            $xml->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            foreach ($xml->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string) $si->t;
                } elseif (isset($si->r)) {
                    $text = '';
                    foreach ($si->r as $run) {
                        $text .= (string) $run->t;
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        $sheetContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetContent === false) {
            $zip->close();
            throw ValidationException::withMessages([
                'batch_file' => 'Excel worksheet not found.',
            ]);
        }

        $sheet = simplexml_load_string($sheetContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        $sheet->registerXPathNamespace('s', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $rowData = [];
            $currentIndex = 0;
            foreach ($row->c as $cell) {
                $cellRef = (string) $cell['r'];
                $targetIndex = $this->columnIndexFromReference($cellRef);

                while ($currentIndex < $targetIndex) {
                    $rowData[] = '';
                    $currentIndex++;
                }

                $rowData[] = $this->convertCellValue($cell, $sharedStrings);
                $currentIndex++;
            }
            $rows[] = $rowData;
        }

        $zip->close();

        return $rows;
    }

    private function columnIndexFromReference(string $reference): int
    {
        $letters = preg_replace('/[^A-Z]/i', '', strtoupper($reference));
        $index = 0;

        foreach (str_split($letters) as $char) {
            $index = ($index * 26) + (ord($char) - 64);
        }

        return max(0, $index - 1);
    }

    private function convertCellValue(\SimpleXMLElement $cell, array $sharedStrings): string
    {
        $value = (string) $cell->v;
        $type = (string) $cell['t'];

        if ($type === 's') {
            return $sharedStrings[(int) $value] ?? '';
        }

        return $value;
    }
}
