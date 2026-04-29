<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\MappingMenu;
use Illuminate\Http\Request;
use App\Models\Master\LoginUser;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function indexMenus()
    {
        $menus = MappingMenu::whereRaw('statusenabled IS TRUE')
            ->where('kdprofile', '10')
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->whereRaw('statusenabled IS TRUE')->orderBy('urutan');
            }])
            ->orderBy('urutan')
            ->get();

        return response()->json([
            'data' => $menus,
        ]);
    }
    public function getMenusByUser($pegawaiId)
    {
        $user = LoginUser::with(['pegawai.jenisPegawai'])
            ->where('objectpegawaifk', $pegawaiId)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "Login user untuk pegawai ID {$pegawaiId} tidak ditemukan"
            ], 404);
        }

        $menusUser = MappingMenu::whereIn('id', function ($q) use ($pegawaiId) {
            $q->select('um.menufk')
                ->from('user_menu as um')
                ->join('loginuser_m as lu', 'lu.id', '=', 'um.loginuserfk')
                // ->where('lu.objectpegawaifk', $pegawaiId)
                ->where('lu.objectpegawaifk', (int)$pegawaiId)
                ->whereRaw('um.statusenabled IS TRUE');
        })->get();

        if ($menusUser->isEmpty() && isset($user->pegawai->objectjenispegawaifk)) {
            $menusUser = MappingMenu::whereIn('id', function ($q) use ($user) {
                $q->select('menufk')
                    ->from('role_menu')
                    ->where('jenispegawaifk', $user->pegawai->objectjenispegawaifk)
                    ->whereRaw('statusenabled IS TRUE');
            })->get();
        }

        $allMenuIds = $menusUser->pluck('id')->toArray();
        $parentIds = $this->getParentIdsRecursive($allMenuIds);
        $allIds = array_unique(array_merge($allMenuIds, $parentIds));

            $menus = collect();
            if (!empty($allIds)) {
                $menus = MappingMenu::whereIn('id', $allIds)
                    ->whereRaw('statusenabled IS TRUE')
                    ->orderBy('urutan')
                    ->get();
            }

            // Fallback: jika user belum punya mapping user/role, tampilkan menu default aktif.
            if ($menus->isEmpty()) {
                $menus = MappingMenu::whereRaw('statusenabled IS TRUE')
                    ->where('kdprofile', '10')
                    ->orderBy('urutan')
                    ->get();
            }

        $tree = $this->buildTree($menus);

        return response()->json([
            'success' => true,
            'data' => $tree,
            'mapping_type' => $menusUser->isEmpty() ? 'role' : 'user',
            'jenis_pegawai' => $user->pegawai->jenisPegawai->jenispegawai ?? null,
            'nama_pegawai' => $user->pegawai->namalengkap ?? null,
        ]);
    }

    private function getParentIdsRecursive(array $menuIds)
    {
        $parentIds = [];

        $parents = MappingMenu::whereIn('id', $menuIds)
            ->pluck('parent_id')
            ->filter()
            ->toArray();

        if (!empty($parents)) {
            $parentIds = array_merge($parentIds, $parents, $this->getParentIdsRecursive($parents));
        }

        return array_unique($parentIds);
    }

    private function buildTree($menus, $parentId = null)
    {
        return $menus->where('parent_id', $parentId)->map(function ($menu) use ($menus) {
            return [
                'id' => $menu->id,
                'nama_menu' => $menu->nama_menu,
                'url' => $menu->url,
                'icon' => $menu->icon,
                'children' => $this->buildTree($menus, $menu->id)
            ];
        })->values();
    }

    public function updateMenu(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:mapping_menu,id',
            'nama_menu' => 'required|string|max:150',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|integer|different:id|exists:mapping_menu,id',
        ]);

        $menu = MappingMenu::findOrFail($validated['id']);
        $menu->nama_menu = $validated['nama_menu'];
        $menu->icon = $validated['icon'] ?? null;
        $menu->parent_id = $validated['parent_id'] ?? null;
        $menu->save();

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diperbarui',
            'data' => $menu
        ]);
    }

    public function setMappingMenu(Request $request)
    {

        if ($request->has('type') && $request->input('type') === 'role') {
            $request->merge(['type' => 'jenispegawai']);
        }

        $validated = $request->validate([
            'type' => 'required|in:user,jenispegawai',
            'mode' => 'required|in:add,edit',
            'entity_id' => 'required|integer',
            'menus' => 'required|array|min:1',
            'menus.*.id' => 'required|integer',
            'menus.*.can_view' => 'boolean',
            'menus.*.can_add' => 'boolean',
            'menus.*.can_edit' => 'boolean',
            'menus.*.can_delete' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $kdProfile = '10';
            if ($validated['type'] === 'user') {

                $pegawai = DB::table('pegawai_m')
                    ->where('id', $validated['entity_id'])
                    ->first();

                if (!$pegawai) {
                    throw new \Exception("Pegawai ID {$validated['entity_id']} tidak ditemukan");
                }

                $loginUser = DB::table('loginuser_m')
                    ->where('objectpegawaifk', $validated['entity_id'])
                    ->first();

                if (!$loginUser) {
                    throw new \Exception("Login user untuk pegawai ID {$validated['entity_id']} tidak ditemukan");
                }

                $loginUserId = $loginUser->id;

                DB::table('user_menu')
                    ->where('loginuserfk', $loginUserId)
                    ->delete();

                foreach ($validated['menus'] as $menu) {
                    DB::table('user_menu')->insert([
                        'kdprofile' => $kdProfile,
                        'statusenabled' => true,
                        'loginuserfk' => $loginUserId,
                        'menufk' => $menu['id'],
                        'can_view' => $menu['can_view'] ?? true,
                        'can_add' => $menu['can_add'] ?? false,
                        'can_edit' => $menu['can_edit'] ?? false,
                        'can_delete' => $menu['can_delete'] ?? false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if ($validated['mode'] === 'add') {
                    $logMessage = "Create Mapping menu dengan jenis mapping 'User' untuk {$pegawai->namalengkap} (Pegawai id: {$pegawai->id})";
                    create_log(3, json_encode([$logMessage], JSON_UNESCAPED_UNICODE));
                } else if ($validated['mode'] === 'edit') {
                    $logMessage = "Update Mapping menu dengan jenis mapping 'User' untuk {$pegawai->namalengkap} (Pegawai id: {$pegawai->id})";
                    create_log(4, json_encode([$logMessage], JSON_UNESCAPED_UNICODE));
                }
            } else {

                DB::table('role_menu')
                    ->where('jenispegawaifk', $validated['entity_id'])
                    ->delete();

                foreach ($validated['menus'] as $menu) {
                    DB::table('role_menu')->insert([
                        'kdprofile' => $kdProfile,
                        'statusenabled' => true,
                        'jenispegawaifk' => $validated['entity_id'],
                        'menufk' => $menu['id'],
                        'can_view' => $menu['can_view'] ?? true,
                        'can_add' => $menu['can_add'] ?? false,
                        'can_edit' => $menu['can_edit'] ?? false,
                        'can_delete' => $menu['can_delete'] ?? false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $jenisPegawai = DB::table('jenispegawai_m')
                    ->where('id', $validated['entity_id'])
                    ->first();
                if ($validated['mode'] === 'add') {
                    $logMessage = "Create Mapping menu dengan jenis mapping 'Role' untuk '{$jenisPegawai->jenispegawai}' (id: {$jenisPegawai->id})";
                    create_log(3, json_encode([$logMessage], JSON_UNESCAPED_UNICODE));
                } else if ($validated['mode'] === 'edit') {
                    $logMessage = "Update Mapping menu dengan jenis mapping 'Role' untuk '{$jenisPegawai->jenispegawai}' (id: {$jenisPegawai->id})";
                    create_log(4, json_encode([$logMessage], JSON_UNESCAPED_UNICODE));
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mapping menu berhasil disimpan',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan mapping menu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getMappingMenu(Request $request)
    {
        $type = $request->query('type');
        $entityId = $request->query('entity_id');


        if (!empty($type) && !empty($entityId)) {

            $menus = [];

            if ($type === 'user') {

                $pegawai = DB::table('pegawai_m')->where('id', $entityId)->first();


                $loginUsers = DB::table('loginuser_m')->where('objectpegawaifk', $entityId)->select('id', 'kodelogin', 'namauser')->get();


                $queryResult = DB::table('user_menu as um')
                    ->join('loginuser_m as lu', 'um.loginuserfk', '=', 'lu.id')
                    ->leftJoin('pegawai_m as p', 'lu.objectpegawaifk', '=', 'p.id')
                    ->join('mapping_menu as mm', 'um.menufk', '=', 'mm.id')
                    ->where('lu.objectpegawaifk', $entityId)
                    ->whereRaw('um.statusenabled IS TRUE')
                    ->select(
                        'um.kdprofile',
                        'um.statusenabled',
                        'um.menufk',
                        'mm.nama_menu',
                        'lu.id AS loginuser_id',
                        'lu.kodelogin',
                        'lu.namauser',
                        'p.id AS pegawai_id',
                        'p.namalengkap'
                    )
                    ->get();

                $menus = $queryResult->pluck('nama_menu')->unique()->toArray();
            } elseif ($type === 'role') {

                $queryResult = DB::table('role_menu as rm')
                    ->join('jenispegawai_m as jp', 'rm.jenispegawaifk', '=', 'jp.id')
                    ->join('mapping_menu as mm', 'rm.menufk', '=', 'mm.id')
                    ->where('rm.jenispegawaifk', $entityId)
                    ->whereRaw('rm.statusenabled IS TRUE')
                    ->select('rm.id', 'mm.nama_menu', 'jp.id as entity_id', 'jp.jenispegawai')
                    ->get();

                $menus = $queryResult->pluck('nama_menu')->unique()->toArray();
            }


            $formattedMenus = [];
            foreach ($menus as $name) {
                $formattedMenus[] = ['name' => $name];
            }

            return response()->json([
                'success' => true,
                'data' => ['menus' => $formattedMenus]
            ]);
        }


        $userMappings = DB::table('user_menu as um')
            ->join('loginuser_m as lu', 'lu.id', '=', 'um.loginuserfk')
            ->join('mapping_menu as mm', 'mm.id', '=', 'um.menufk')
            ->leftJoin('pegawai_m as pg', 'pg.id', '=', 'lu.objectpegawaifk')
            ->select(
                'um.id',
                'pg.id as entity_id',
                'pg.namalengkap as name',
                'mm.nama_menu as menufk',
                'um.created_at',
                'um.updated_at',
                'lu.namauser as entity_name',
                DB::raw("'user' as type")
            )
            ->get();

        $roleMappings = DB::table('role_menu as rm')
            ->join('jenispegawai_m as jp', 'jp.id', '=', 'rm.jenispegawaifk')
            ->join('mapping_menu as mm', 'mm.id', '=', 'rm.menufk')
            ->select(
                'rm.id',
                'jp.id as entity_id',
                'jp.jenispegawai as name',
                'mm.nama_menu as menufk',
                'rm.created_at',
                'rm.updated_at',
                'jp.jenispegawai as entity_name',
                DB::raw("'role' as type")
            )
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user_mapping' => $userMappings,
                'role_mapping' => $roleMappings,
            ]
        ]);
    }

    public function getEditMenusByUser($userId)
    {

        $user = LoginUser::with(['pegawai.jenisPegawai'])->findOrFail($userId);
        $jenisMapping = 'user';

        $menusUser = MappingMenu::whereIn('id', function ($q) use ($userId) {
            $q->select('um.menufk')
                ->from('user_menu as um')
                ->join('loginuser_m as lu', 'lu.id', '=', 'um.loginuserfk')
                ->where('lu.objectpegawaifk', $userId);
        })->get();


        if ($menusUser->isEmpty() && isset($user->pegawai->jenispegawaifk)) {
            $jenisMapping = 'role';
            $menusUser = MappingMenu::whereIn('id', function ($q) use ($user) {
                $q->select('menufk')
                    ->from('role_menu')
                    ->where('jenispegawaifk', $user->pegawai->jenispegawaifk);
            })->get();
        }

        $children = $menusUser
            ->whereNotNull('parent_id')
            ->whereRaw('statusenabled IS TRUE')
            ->sortBy('urutan')
            ->map(fn($menu) => [
                'id' => $menu->id,
                'nama_menu' => $menu->nama_menu
            ])
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'jenis_mapping' => $jenisMapping,
            'data' => $children
        ]);
    }
    public function deleteMenuMapping(Request $request)
    {
        $request->validate([
            'type' => 'required|in:user,role',
            'entity_id' => 'required|integer|min:1'
        ]);

        $pegawai = DB::table('pegawai_m')
            ->where('id', $request['entity_id'])
            ->first();

        $role = DB::table('jenispegawai_m')
            ->where('id', $request['entity_id'])
            ->first();

        $type = $request->type;
        $entityId = $request->entity_id;

        DB::beginTransaction();
        try {
            if ($type === 'user') {
                $loginUserIds = DB::table('loginuser_m')->where('objectpegawaifk', $entityId)->pluck('id');
                $updated = DB::table('user_menu')
                    ->whereIn('loginuserfk', $loginUserIds)
                    ->update(['statusenabled' => false, 'updated_at' => now()]);
            } elseif ($type === 'role') {
                $updated = DB::table('role_menu')
                    ->where('jenispegawaifk', $entityId)
                    ->update(['statusenabled' => false, 'updated_at' => now()]);
            }

            $logMessage = $pegawai?->namalengkap
                ? "Delete Mapping menu dengan jenis mapping '" . ucfirst($request['type']) . "' untuk '{$pegawai->namalengkap}' (id: {$pegawai->id})"
                : "Delete Mapping menu dengan jenis mapping '" . ucfirst($request['type']) . "' untuk '{$request['type']}' untuk role '{$role->jenispegawai}' (id: {$role->id})";

            create_log(5, json_encode([$logMessage], JSON_UNESCAPED_UNICODE));

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Mapping soft deleted successfully',
                'updated_count' => $updated ?? 0
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function getAllMappings()
    {

        $userMappings = DB::table('user_menu as um')
            ->join('loginuser_m as lu', 'lu.id', '=', 'um.loginuserfk')
            ->join('mapping_menu as mm', 'mm.id', '=', 'um.menufk')
            ->leftJoin('pegawai_m as pg', 'pg.id', '=', 'lu.objectpegawaifk')
            ->whereRaw('um.statusenabled IS TRUE')
            ->select(
                'um.id',
                'pg.id as entity_id',
                'pg.namalengkap as name',
                'mm.nama_menu as menufk',
                'um.created_at',
                'um.updated_at',
                'lu.namauser as entity_name',
                DB::raw("'user' as type")
            )
            ->get();


        $roleMappings = DB::table('role_menu as rm')
            ->join('jenispegawai_m as jp', 'jp.id', '=', 'rm.jenispegawaifk')
            ->join('mapping_menu as mm', 'mm.id', '=', 'rm.menufk')
            ->whereRaw('rm.statusenabled IS TRUE')
            ->select(
                'rm.id',
                'jp.id as entity_id',
                'jp.jenispegawai as name',
                'mm.nama_menu as menufk',
                'rm.created_at',
                'rm.updated_at',
                'jp.jenispegawai as entity_name',
                DB::raw("'role' as type")
            )
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user_mapping' => $userMappings,
                'role_mapping' => $roleMappings,
            ]
        ]);
    }
}



