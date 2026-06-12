<?php

namespace App\Api\AdminV1\Http\Controllers\Sidebar;

use App\Api\AdminV1\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SidebarController extends Controller
{
    /**
     * Get all unique permissions of current user
     */
    public function permissions(Request $request)
    {
        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin-api')->user();

        // Get all permissions (direct + from roles) and make unique
        $permissions = $admin->getAllPermissions()->pluck('name')->unique()->values();

        // Merge mevivuDev into permissions
        $permissions->push('mevivuDev');

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $permissions,
        ]);
    }
}
