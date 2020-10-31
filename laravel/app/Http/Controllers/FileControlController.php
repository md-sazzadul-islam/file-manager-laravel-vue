<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CheckRequest;
use App\Http\Helper\FileManager;

class FileControlController extends Controller
{
    public $filecontrol;

    public function __construct(FileManager $filecontrol)
    {
        $this->filecontrol = $filecontrol;
    }

    public function content(CheckRequest $request)
    {
        return response()->json(
            $this->filecontrol->content(
                $request->input('disk'),
                $request->input('path')
            )
        );
    }

    public function tree(CheckRequest $request)
    {
        return response()->json(
            $this->filecontrol->tree(
                $request->input('disk'),
                $request->input('path')
            )
        );
    }

    public function upload(CheckRequest $request)
    {
        // dd($request->file('files'));
        // return response()->json($request->all());
        $uploadResponse = $this->filecontrol->upload(
            $request->input('disk'),
            $request->input('path'),
            $request->file('files')
        );

        return response()->json($uploadResponse);
    }

    public function delete(CheckRequest $request)
    {
        $deleteResponse = $this->filecontrol->delete(
            $request->input('disk'),
            $request->input('type'),
            $request->input('path')
        );

    }

    public function paste(CheckRequest $request)
    {
        return response()->json(
            $this->filecontrol->paste(
                $request->input('disk'),
                $request->input('path'),
                $request->input('clipboard')
            )
        );
    }

    public function rename(CheckRequest $request)
    {
        return response()->json(
            $this->filecontrol->rename(
                $request->input('disk'),
                $request->input('newName'),
                $request->input('oldName')
            )
        );
    }

    public function download(CheckRequest $request)
    {
        return $this->filecontrol->download(
            $request->input('disk'),
            $request->input('path')
        );
    }
    public function thumbnails(CheckRequest $request)
    {
        return $this->filecontrol->thumbnails(
            $request->input('disk'),
            $request->input('path')
        );
    }

    public function createDirectory(CheckRequest $request)
    {
        $createDirectoryResponse = $this->filecontrol->createDirectory(
            $request->input('disk'),
            $request->input('path'),
            $request->input('name')
        );


        return response()->json($createDirectoryResponse);
    }

    public function createFile(CheckRequest $request)
    {
        $createFileResponse = $this->filecontrol->createFile(
            $request->input('disk'),
            $request->input('path'),
            $request->input('name')
        );

        return response()->json($createFileResponse);
    }

}
