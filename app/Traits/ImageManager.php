<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait ImageManager
{
    // متدهای مدیریت تصویر
    public function addImage(Request $request, $model): void
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
            $disk = 'sftp'; // دیسک مورد استفاده (باید در config/filesystems.php تعریف شود)
            $result = Storage::putFileAs('uploads/images', $fileData, $newFileName);

            Log::info('File upload result:', ['path' => $result]);


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

    public function deletedImageIfExist($model): void
    {
            if ($model->image) {

                $disk = 'sftp'; // دیسک FTP
                Log::info('test');
                Log::info('$model->image->file_path', [Storage::path($model->image->file_path)]);

                // حذف فایل از FTP
                if (Storage::exists($model->image->file_path)) {
                    Storage::delete($model->image->file_path);
                }
                /* // حذف فایل از سیستم
                 if (file_exists(public_path( $model->image->file_path))) {
                     unlink(public_path( $model->image->file_path));
                 }*/
                // حذف رکورد فایل از دیتابیس
                $model->image->delete();
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
                $disk = 'sftp'; // دیسک FTP
                $filePath = $model->image->file_path;

                // لاگ مسیر فایل
                Log::info('Attempting to delete file from FTP', ['file_path' => $filePath]);

                try {
                    // بررسی وجود فایل و حذف آن
                    if (Storage::exists($filePath)) {
                        Storage::delete($filePath);
                        Log::info('File deleted successfully', ['file_path' => $filePath]);
                    } else {
                        Log::warning('File not found on FTP', ['file_path' => $filePath]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error deleting file from FTP', [
                        'file_path' => $filePath,
                        'exception' => $e->getMessage(),
                    ]);
                }

                // حذف رکورد فایل از دیتابیس
                $model->image->delete();
            }

            // افزودن تصویر جدید
            $this->addImage($request, $model);
        }
    }
}
