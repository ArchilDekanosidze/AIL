<?php
namespace App\Http\Controllers\Admin\Import;


use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class DatabaseExportController extends Controller
{
    public function exportDatabase()
    {
        // Define the directory to store SQL files
        $directory = public_path('db/');
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Define the chunk size
        $chunkSize = 1000; // Adjust as needed

        // Retrieve the total number of rows in the table
        $totalRows = DB::table('questions_temps')->count();

        // Loop through the table in chunks
        $chunks = ceil($totalRows / $chunkSize);
        $zipFilePaths = [];
        for ($i = 0; $i < $chunks; $i++) {
            // Calculate the offset for this chunk
            $offset = $i * $chunkSize;

            // Fetch a chunk of rows from the table
            $rows = DB::table('questions_temps')
                ->offset($offset)
                ->limit($chunkSize)
                ->get();

            // Prepare the SQL filename
            $sqlFilename = "database_backup_chunk_{$i}.sql";
            $sqlFilePath = $directory . DIRECTORY_SEPARATOR . $sqlFilename;

            // Open the SQL file for writing
            $file = fopen($sqlFilePath, 'w');

            // Write INSERT statements to the file
            foreach ($rows as $row) {
                $columns = implode(", ", array_keys((array)$row));
                $values = "'" . implode("', '", array_values((array)$row)) . "'";
                $insertSql = "INSERT INTO questions_temps ({$columns}) VALUES ({$values});\n";
                fwrite($file, $insertSql);
            }

            fclose($file);

            // Create a ZIP file for this chunk
            $zipFilename = "database_backup_chunk_{$i}.sql.zip";
            $zipFilePath = $directory . DIRECTORY_SEPARATOR . $zipFilename;
            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE) === true) {
                $zip->addFile($sqlFilePath, $sqlFilename);
                $zip->close();
                unlink($sqlFilePath); // Delete the SQL file after adding to ZIP
                $zipFilePaths[] = $zipFilePath;
            } else {
                // Handle ZIP creation failure
                // Log the error or notify the user
            }
        }

        // Return the ZIP files as a download
        // return response()->download($zipFilePaths[0])->deleteFileAfterSend(true);
    }
}