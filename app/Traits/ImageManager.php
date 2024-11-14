<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Http\Request;

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
            $filePath = $fileData->storeAs('uploads/images', $newFileName, 'public');


            $file = new File([
                'file_name' => $newFileName,
                'file_path' => '/storage/' . $filePath,
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
            // حذف فایل از سیستم
            if (file_exists(public_path( $model->image->file_path))) {
                unlink(public_path( $model->image->file_path));
            }
            // حذف رکورد فایل از دیتابیس
            $model->image->delete();
        }}
    }
    /**
     * @param $model
     * @return void
     */
    public function updatedImageIfExist(Request $request, $model): void
    {


        if ($request->hasFile('image')) {

            if ($model->image) {
                // حذف فایل از سیستم
                if (file_exists(public_path( $model->image->file_path))) {
                    unlink(public_path( $model->image->file_path));
                }
                // حذف رکورد فایل از دیتابیس
                $model->image->delete();

            }
            $this->addImage($request,$model);
        }
    }
}
