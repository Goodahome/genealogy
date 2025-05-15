<?php

declare(strict_types=1);

namespace App;

use App\Models\Person;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

final class PersonPhotos
{
    private readonly ImageManager $imageManager;

    private array $config;

    public function __construct(private readonly Person $person)
    {
        $this->imageManager = new ImageManager(new Driver);

        $this->config = config('app.image_upload');
    }

    public function save(array $photos): void
    {
        if (empty($photos)) {
            return;
        }

        $this->ensureDirectoriesExist();

        $lastIndex = $this->getLastImageIndex();

        foreach ($photos as $photo) {
            $this->savePhoto($photo, ++$lastIndex);
        }

        $this->cleanupTemporaryFiles();
    }

    private function ensureDirectoriesExist(): void
    {
        $teamId = (string) $this->person->team_id;

        foreach (config('app.photo_folders') as $folder) {
            if (! Storage::disk($folder)->exists($teamId)) {
                Storage::disk($folder)->makeDirectory($teamId);
            }
        }
    }

    private function getLastImageIndex(): int
    {
        // 修改查找器以支持多种图片格式
        $files = File::glob(public_path() . '/storage/photos/' . $this->person->team_id . '/' . $this->person->id . '_*.{webp,gif}', GLOB_BRACE);
    
        if ($files) {
            $lastFile = last($files);
            return (int) mb_substr((string) $lastFile, mb_strpos((string) $lastFile, '_') + 1, mb_strrpos((string) $lastFile, '_') - mb_strpos((string) $lastFile, '_') - 1);
        }
        return 0;
    }

    private function savePhoto($photo, int $index): void
    {
        $timestamp = now()->format('YmdHis');
        
        // 获取原始文件扩展名
        $originalExtension = $photo->getClientOriginalExtension();
        $isGif = strtolower($originalExtension) === 'gif';
        
        // 如果是GIF，保留原始扩展名，否则使用配置中的类型
        $extension = $isGif ? 'gif' : $this->config['type'];
        $imageName = "{$this->person->id}_" . mb_str_pad((string) $index, 3, '0', STR_PAD_LEFT) . "_{$timestamp}.{$extension}";
    
        $this->processAndSaveImage(
            photo: $photo,
            imageName: $imageName,
            addWatermark: $this->config['add_watermark'] && !$isGif // GIF不添加水印
        );
    
        if (empty($this->person->photo)) {
            $this->person->update(['photo' => $imageName]);
        }
    }
    
    private function processAndSaveImage($photo, string $imageName, bool $addWatermark = false): void
    {
        $paths = [
            'photos' => [
                'width'  => $this->config['max_width'],
                'height' => $this->config['max_height'],
            ],
            'photos-096' => [
                'width'  => 96,
                'height' => null,
            ],
            'photos-384' => [
                'width'  => 384,
                'height' => null,
            ],
        ];
        
        // 检查是否为GIF文件
        $isGif = pathinfo($imageName, PATHINFO_EXTENSION) === 'gif';
        
        if ($isGif) {
            // 对于GIF文件，直接保存原始文件而不进行处理
            foreach ($paths as $disk => $dimensions) {
                Storage::disk($disk)->putFileAs(
                    $this->person->team_id,
                    $photo,
                    $imageName
                );
            }
        } else {
            // 对于非GIF文件，按原来的方式处理
            foreach ($paths as $disk => $dimensions) {
                $image = $this->imageManager
                    ->read($photo)
                    ->scaleDown(
                        width: $dimensions['width'],
                        height: $dimensions['height']
                    );
    
                if ($addWatermark) {
                    $image->place(public_path('img/watermark.png'), 'bottom-left', 5, 5);
                    // 获取当前登录用户名
                    // $userName = auth()->user()->name ?? 'Genealogy';
                    
                    // // 添加文本水印
                    // $image->text($userName, 
                    //     x: 10, 
                    //     y: $image->height() - 10, 
                    //     font: function ($font) {
                    //         $font->file(public_path('fonts/SIMSUN.TTC'));
                    //         $font->size(16);
                    //         $font->color('#ffffff');
                    //         $font->align('left');
                    //         $font->valign('bottom');
                    //     }
                    // );
                }
    
                $image
                    ->toWebp(quality: $this->config['quality'])
                    ->save(storage_path("app/public/{$disk}/{$this->person->team_id}/{$imageName}"));
            }
        }
    }

    private function cleanupTemporaryFiles(): void
    {
        $oneDayAgo = now()->subDay()->timestamp;

        defer(function () use ($oneDayAgo): void {
            foreach (Storage::files('livewire-tmp') as $file) {
                if (Storage::lastModified($file) < $oneDayAgo) {
                    Storage::delete($file);
                }
            }
        });
    }
}