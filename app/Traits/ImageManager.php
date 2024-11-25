<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait ImageManager
{
    // متدهای مدیریت تصویر
    public function addImage(Request $request,$model): void
    {

        if ($request->hasFile('image')) {

            $fileData = $request->file('image');
            // گرفتن پسوند فایل
            $extension = $fileData->getClientOriginalExtension();
            // ساختن نام جدید برای فایل براساس id کاربر و تاریخ جاری
            //$uuidPart = substr($user->id, 0, 8); // اولین ۸ کاراکتر از UUID
            $newFileName = $model->id . '_' . \Carbon\Carbon::now()->format('Ymd_His') . '.' . $extension;


            // ذخیره فایل در پوشه uploads/images
            //$filePath = $fileData->storeAs('uploads/images', $newFileName, 'public');
            // ذخیره فایل در FTP
            $filePath = '/uploads/images/' . $newFileName;
            $disk = 'ftp'; // دیسک مورد استفاده (باید در config/filesystems.php تعریف شود)
            Storage::disk($disk)->putFileAs('uploads/images', $fileData, $newFileName);



            $file = new File([
                'file_name' => $newFileName,
                //'file_path' => '/storage/' . $filePath,
                'file_path' => $filePath,
                'file_type' => $fileData->getMimeType(),
                'file_size' => $fileData->getSize(),
            ]);
            ///add image to user
            $model->image()->save($file);
        }
    }
    public function deletedImageIfExist(Request $request, $model): void
    {
        if ($request->hasFile('image')) {
        if ($model->image) {

            $disk = 'ftp'; // دیسک FTP
            Log::info($model->image->file_path, [Storage::disk('ftp')->path($model->image->file_path)]);

            // حذف فایل از FTP
            if (Storage::disk($disk)->exists($model->image->file_path)) {
                Storage::disk($disk)->delete($model->image->file_path);
            }
           /* // حذف فایل از سیستم
            if (file_exists(public_path( $model->image->file_path))) {
                unlink(public_path( $model->image->file_path));
            }*/
            // حذف رکورد فایل از دیتابیس
            $model->image->delete();
        }
        }
    }

    /**
     * @param Request $request
     * @param $model
     * @return void
     */
    public function updatedImageIfExist(Request $request, $model): void
    {


        if ($request->hasFile('image')) {

            if ($model->image) {

                $disk = 'ftp'; // دیسک FTP
                // حذف فایل از FTP
                if (Storage::disk($disk)->exists($model->image->file_path)) {
                    Storage::disk($disk)->delete($model->image->file_path);
                }
               /* // حذف فایل از سیستم
                if (file_exists(public_path( $model->image->file_path))) {
                    unlink(public_path( $model->image->file_path));
                }*/
                // حذف رکورد فایل از دیتابیس
                $model->image->delete();

            }
            $this->addImage($request,$model);
        }
    }
}
