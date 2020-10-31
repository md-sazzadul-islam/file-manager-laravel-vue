<?php

namespace App\Http\Services\TransferService;

use Illuminate\Support\Arr;
use Storage;

trait ContentTrait
{

    public function getContent($disk, $path = null)
    {
        $content = Storage::disk($disk)->listContents($path);

        $directories = $this->filterDir($disk, $content);

        $files = $this->filterFile($disk, $content);

        return compact('directories', 'files');
    }

    public function directoriesWithProperties($disk, $path = null)
    {
        $content = Storage::disk($disk)->listContents($path);

        return $this->filterDir($disk, $content);
    }

    public function filesWithProperties($disk, $path = null)
    {
        $content = Storage::disk($disk)->listContents($path);

        return $this->filterFile($disk, $content);
    }

    public function getDirectoriesTree($disk, $path = null)
    {
        $directories = $this->directoriesWithProperties($disk, $path);

        foreach ($directories as $index => $dir) {
            $sub_tree = array();
            $subm = Storage::disk($disk)->directories($dir['path']) ? true : false;
            if($subm){
                $sub_path = $dir['path'];
                $sub_tree = $this->getDirectoriesTree($disk, $sub_path);
            }
            $directories[$index]['hasSubdirectoriesTree'] = $sub_tree;
            $directories[$index]['props'] = [
                'hasSubdirectories' => $subm,
                // 'hasSubdirectoriesTree' => $sub_tree,
            ];
        }

        return $directories;
    }

    public function fileProperties($disk, $path = null)
    {
        $file = Storage::disk($disk)->getMetadata($path);

        $pathInfo = pathinfo($path);

        $file['basename'] = $pathInfo['basename'];
        $file['dirname'] = $pathInfo['dirname'] === '.' ? ''
            : $pathInfo['dirname'];
        $file['extension'] = isset($pathInfo['extension'])
            ? $pathInfo['extension'] : '';
        $file['filename'] = $pathInfo['filename'];

       

        return $file;
    }

    public function directoryProperties($disk, $path = null)
    {
        $directory = Storage::disk($disk)->getMetadata($path);

        $pathInfo = pathinfo($path);

        if (!$directory) {
            $directory['path'] = $path;
            $directory['type'] = 'dir';
        }

        $directory['basename'] = $pathInfo['basename'];
        $directory['dirname'] = $pathInfo['dirname'] === '.' ? ''
            : $pathInfo['dirname'];


        return $directory;
    }

    protected function filterDir($disk, $content)
    {
        $dirsList = Arr::where($content, function ($item) {
            return $item['type'] === 'dir';
        });

        $dirs = array_map(function ($item) {
            return Arr::except($item, ['filename']);
        }, $dirsList);

        

        return array_values($dirs);
    }

    protected function filterFile($disk, $content)
    {
        $files = Arr::where($content, function ($item) {
            return $item['type'] === 'file';
        });

        return array_values($files);
    }

}
