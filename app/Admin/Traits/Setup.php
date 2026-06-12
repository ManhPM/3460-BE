<?php

namespace App\Admin\Traits;

use Illuminate\Support\Str;

trait Setup
{
    public function uniqidReal($lenght = 13)
    {
        return uniqid_real($lenght);
    }

    public function createCodeUser()
    {
        return 'U' . $this->uniqidReal(5) . time();
    }
    public function createAffiliateCode()
    {
        return 'AF' . $this->uniqidReal(2) . time();
    }
    public function createCodeOrder()
    {
        return 'HD' . $this->uniqidReal(6);
    }

    public function createCodeVoucher()
    {
        return 'VOUCHER' . $this->uniqidReal(6);
    }

    public function createCodePayment()
    {
        return 'P' . $this->uniqidReal(5) . time();
    }

    public function folderUploadFileForUser($path = '/')
    {
        $path = $path == '/' ? '/' : '/' . Str::finish($path, '/');

        return 'users/' . auth()->user()->id . $path;
    }

    public function generateTokenGetPassword()
    {
        return (string) Str::uuid() . '-' . time();
    }
}
