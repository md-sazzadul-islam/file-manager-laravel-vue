<?php

namespace App\Http\Helper;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Storage;
// use Image;
use Intervention\Image\Facades\Image as Image;
use App\Http\Services\TransferService\TransferFactory;
use App\Http\Services\TransferService\PathTrait;
use App\Http\Services\TransferService\ContentTrait;

class FileManager
{
    use PathTrait,ContentTrait;
    
    public function content($disk, $path)
    {

        $content = $this->getContent($disk, $path);

        return [
            'result'      => [
                'status'  => 'success',
                'message' => null,
            ],
            'directories' => $content['directories'],
            'files'       => $content['files'],
        ];
    }
    public function getContent($disk, $path = null)
    {
        $content = Storage::disk($disk)->listContents($path);

        $directories = $this->filterDir($disk, $content);

        $files = $this->filterFile($disk, $content);

        return compact('directories', 'files');
    }
    public function tree($disk, $path)
    {
        $directories = $this->getDirectoriesTree($disk, $path);

        return [
            'result'      => [
                'status'  => 'success',
                'message' => null,
            ],
            'directories' => $directories,
        ];
    }
    public function upload($disk, $path, $files)
    {
        foreach ($files as $file) {
        
            $getFileMaxSize = config('file-config.maxUploadFileSize');
            if ($getFileMaxSize
                && $file->getSize() / 1024 > $getFileMaxSize
            ) {
                
                return [
                    'result' => [
                        'status'  => 'warning',
                        'message' => 'Max File Size '.config('file-config.maxUploadFileSize'),
                    ],
                ];
            }
            $getAllowFileTypes = config('file-config.allowFileTypes');

            if ($getAllowFileTypes
                && !in_array(
                    $file->getClientOriginalExtension(),
                    $getAllowFileTypes
                )
            ) {
                return [
                    'result' => [
                        'status'  => 'warning',
                        'message' => 'Allow File Types : '.implode(",",config('file-config.allowFileTypes')),
                    ],
                ];
            }

            Storage::disk($disk)->putFileAs(
                $path,
                $file,
                $file->getClientOriginalName()
            );
        }

        return [
            'result' => [
                'status'  => 'success',
                'message' => 'uploaded',
            ],
        ];
    }

    public function delete($disk, $type, $path)
    {
            if (!Storage::disk($disk)->exists($path)) {
                return [
                    'result' => [
                        'status'  => 'error',
                        'message' => 'File not found',
                    ],
                ];
            } else {
                if ($type === 'dir') {
                    Storage::disk($disk)->deleteDirectory($path);
                } else {
                    Storage::disk($disk)->delete($path);
                }
            }

        return [
            'result' => [
                'status'  => 'success',
                'message' => 'deleted',
            ],
        ];
    }

    public function paste($disk, $path, $clipboard)
    {
        if ($disk !== $clipboard['disk']) {

            if (!$this->checkDisk($clipboard['disk'])) {
                return $this->notFoundMessage();
            }
        }

        $transferService = TransferFactory::build($disk, $path, $clipboard);

        return $transferService->filesTransfer();
    }

    public function rename($disk, $newName, $oldName)
    {
        Storage::disk($disk)->move($oldName, $newName);

        return [
            'result' => [
                'status'  => 'success',
                'message' => 'renamed',
            ],
        ];
    }

    public function download($disk, $path)
    {
        if (!preg_match('/^[\x20-\x7e]*$/', basename($path))) {
            $filename = Str::ascii(basename($path));
        } else {
            $filename = basename($path);
        }

        return Storage::disk($disk)->download($path, $filename);
    }

    public function thumbnails($disk, $path)
    {
        
        $thumbnail = Image::make(Storage::disk($disk)->get($path))->fit(100);

        return $thumbnail->response();
    }

    public function createDirectory($disk, $path, $name)
    {
        $directoryName = $this->newPath($path, $name);

        if (Storage::disk($disk)->exists($directoryName)) {
            return [
                'result' => [
                    'status'  => 'warning',
                    'message' => 'dirExist',
                ],
            ];
        }

        Storage::disk($disk)->makeDirectory($directoryName);

        $directoryProperties = $this->directoryProperties(
            $disk,
            $directoryName
        );

        $tree = $directoryProperties;
        $tree['props'] = ['hasSubdirectories' => false];

        return [
            'result'    => [
                'status'  => 'success',
                'message' => 'dirCreated',
            ],
            'directory' => $directoryProperties,
            'tree'      => [$tree],
        ];
    }

    public function createFile($disk, $path, $name)
    {
        $path = $this->newPath($path, $name);

        if (Storage::disk($disk)->exists($path)) {
            return [
                'result' => [
                    'status'  => 'warning',
                    'message' => 'fileExist',
                ],
            ];
        }

        Storage::disk($disk)->put($path, '');

        $fileProperties = $this->fileProperties($disk, $path);

        return [
            'result' => [
                'status'  => 'success',
                'message' => 'fileCreated',
            ],
            'file'   => $fileProperties,
        ];
    }

}
