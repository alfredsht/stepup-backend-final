<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\MessageSentPublicChannel;
use \Exception;
use App;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;


class NotificationController extends Controller
{
    use  App\Traits\ApiResponse\ApiResponse;
    use App\Traits\LogingSystems\LogingSystems;

    protected function now()
    {
        return Carbon::now('Asia/Jakarta');
    }

    public function getNotifications(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            // tambahkan log untuk ngecek id user yang login
            Log::info('User object from request', ['user' => $user->toArray()]);

            $pegawaiId = $user->objectpegawaifk;

            Log::info('Pegawai ID', ['pegawaiId' => $pegawaiId]);

            $notifications = DB::table('notifikasi_pegawai as np')
                ->join('notifikasi_m as n', 'np.notif_id', '=', 'n.id')
                ->select(
                    'n.id as notif_id',
                    'n.notif_title',
                    'n.notif_detail',
                    'n.notif_type',
                    'n.created_at as notif_created_at',
                    'np.is_read',
                    'np.read_at'
                )
                ->where('np.pegawai_id', $pegawaiId)
                ->whereRaw('n.statusenabled IS TRUE')
                //->whereDate('n.created_at', $this->now())
                //->where('np.is_read', false)
                ->orderBy('n.created_at', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $notifications
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public static function buatNotifikasiTabungan($student, $pegawaiId, $jenisId, $jumlah)
    {
        $pegawai = DB::table('pegawai_m')
            ->where('id', $pegawaiId)
            ->whereRaw('statusenabled IS TRUE')
            ->whereNull('deleted_at')
            ->first();

        if (!$pegawai) {
            return;
        }

        $jenisTransaksi = $jenisId == 1 ? 'menabung' : 'penarikan';
        $jumlahFormatted = number_format($jumlah, 0, ',', '.');

        $notifTitle = 'Tabungan ' . $student->namalengkap;
        $notifDetail = "{$student->namalengkap} {$jenisTransaksi} sebesar Rp {$jumlahFormatted}";
        $notifCreatedAt = now()->format('Y-m-d H:i:s');

        $notifId = DB::table('notifikasi_m')->insertGetId([
            'statusenabled' => DB::raw('true'),
            'kdprofile'     => '10',
            'notif_type'    => 'tabungan',
            'notif_title'   => $notifTitle,
            'notif_detail'  => $notifDetail,
            'is_forall'     => false,
            'created_at'    => now('Asia/Jakarta'),
            'updated_at'    => now('Asia/Jakarta'),
        ]);

        DB::table('notifikasi_pegawai')->insert([
            'notif_id'   => $notifId,
            'pegawai_id' => $pegawai->id,
            'is_read'    => false,
            'created_at' => now('Asia/Jakarta'),
            'updated_at' => now('Asia/Jakarta'),
        ]);

        $notif = DB::table('notifikasi_m')->where('id', $notifId)->first();

        MessageSentPublicChannel::dispatch(
            $notif->notif_title,
            $notif->notif_detail,
            $notif->created_at
        );
    }

    public function markAsRead()
    {
        try {
            $request = request();

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $validated = $request->validate([
                'ids'   => 'required|array',
                'ids.*' => 'integer',
            ]);

            $ids = $validated['ids'];
            $pegawaiId = $user->objectpegawaifk;

            if (empty($ids)) {
                return response()->json([
                    'status' => true,
                    'message' => 'No notifications to mark as read.'
                ]);
            }

            DB::table('notifikasi_pegawai')
                ->where('pegawai_id', $pegawaiId)
                ->whereIn('notif_id', $ids)
                ->update([
                    'is_read' => true,
                    'read_at' => $this->now(),
                    'updated_at' => $this->now(),
                ]);

            return response()->json([
                'status' => true,
                'message' => 'Notifications marked as read'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function deleteNotifications()
    {
        try {
            $request = request();

            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $validated = $request->validate([
                'ids'   => 'required|array',
                'ids.*' => 'integer',
            ]);

            $ids = $validated['ids'];
            $pegawaiId = $user->objectpegawaifk;

            if (empty($ids)) {
                return response()->json([
                    'status' => true,
                    'message' => 'No notifications to delete.'
                ]);
            }

            DB::table('notifikasi_pegawai')
                ->where('pegawai_id', $pegawaiId)
                ->whereIn('notif_id', $ids)
                ->delete();

            return response()->json([
                'status' => true,
                'message' => 'Notifications deleted successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
