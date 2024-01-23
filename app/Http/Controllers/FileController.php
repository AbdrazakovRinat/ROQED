<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Request $request) {
        $searchQuery = $request->input('search', ''); // Получаем поисковый запрос
        // Фильтруем файлы в зависимости от поискового запроса
        $files = File::when($searchQuery, function ($query, $search) {
            return $query->where('filename', 'like', '%' . $search . '%'); // Пример поиска по имени файла
        })->paginate(50);

        return Inertia('Index', [
            'files' => $files,
        ]);
    }

    public function addFile(Request $request){
        return Inertia('AddFile',[
        ]);
    }

    public function editFile(Request $request){
        $file = File::find($request->fileId);
        return Inertia('EditFile',[
            'file'=>$file,
        ]);
    }

    public function addFilePost(Request $request) {
        $file = $request->file('newFile');
        $fileExtension = $file->getClientOriginalExtension();
        $filename = $request->input('filename');
        $originalName = str_replace(' ', '_', $filename);

        // Проверка уникальности имени файла
        $counter = 1;
        $newFilename = $originalName;
        while (File::where('filename', $newFilename)->exists()) {
            // Если файл с таким именем существует, добавляем к имени файла счетчик
            $newFilename = $originalName . '(' . $counter . ')';
            $counter++;
        }

        $path = $file->storeAs('uploads', $newFilename . '.' . $fileExtension, 'public');
        $fileSize = $file->getSize();

        File::create([
            'filename' => $newFilename,
            'size' => $fileSize,
            'fileExtension' => $fileExtension,
            'path' => $path,
            'user_id' => 1, // или другой идентификатор пользователя
        ]);
    }


    public function editFilePost(Request $request){
//        dd($request->all());
        if ($request->file('newFile')){
            $file = File::find($request->input('id'));
            if ($file) {
                $filePath = $file->path;
                // Удаление файла с диска 'public'
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
            $newFile = $request->file('newFile');
            $filename = $request->input('filename'); // Использование обработанного пользовательского имени
            $originalName = str_replace(' ', '_', $filename);
            $fileExtension = $newFile->getClientOriginalExtension(); // Получение расширения файла
            $path = $newFile->storeAs('uploads', $originalName.'.'.$fileExtension, 'public');
            $fileSize = $newFile->getSize();
            $editingFile = File::find($request->input('id'));
            $editingFile->filename = $request->input('filename');
            $editingFile->size = $fileSize;
            $editingFile->fileExtension = $fileExtension;
            $editingFile->path = $path;
            $editingFile->save();
        }else{
            $filename = $request->input('filename');
            $originalName = str_replace(' ', '_', $filename);

            $editingFile = File::find($request->input('id'));
            $editingFile->filename = $request->input('filename');
            // Удаление файла с диска 'public'
            if (Storage::disk('public')->exists($editingFile->path)) {
                $fileExtension = $editingFile->fileExtension;
                $newFileName = $originalName . '.' . $fileExtension;
                $newFilePath = 'uploads/' . $newFileName; // Новый путь и имя файла

                // Переименование файла
                Storage::disk('public')->move($editingFile->path, $newFilePath);

                // Обновление данных в модели
                $editingFile->filename = $filename;
                $editingFile->path = $newFilePath;
                $editingFile->save();
            }

            $editingFile->path = $newFilePath;
            $editingFile->save();
        }
        return to_route('index');
    }

    public function deleteFile(Request $request){
        $fileId = $request->input('fileId');
        $file = File::find($fileId);
        if ($file) {
            // Получаем путь к файлу, относительно корня 'public' диска
            $filePath = $file->path; // как в модели
            // Удаление файла с диска 'public'
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            // Удаление записи из базы данных
            $file->delete();
        }
        return to_route('index');
    }
}
