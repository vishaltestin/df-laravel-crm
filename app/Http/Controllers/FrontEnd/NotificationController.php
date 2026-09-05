<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sentinel;
use App\Notification;

class NotificationController extends Controller
{
    private function getOrgId()
    {
        $reg = registeruser(Sentinel::check()->id);
        return $reg->organization;
    }

    // dropdown list
    public function list(Request $request)
	{
		$user = Sentinel::check();
		$orgId = $this->getOrgId();

		$type = $request->type; // all | message | alert

		$q = Notification::where('organization_id', $orgId)
			->where('user_id', $user->id);

		if ($type === 'message') {
			$q->where('type', 'message');
		} elseif ($type === 'alert') {
			$q->where('type', 'alert');
		}

		$items = $q->orderBy('is_read', 'ASC')
			->orderBy('id', 'DESC')
			->limit(20)
			->get();

		$unreadCount = Notification::where('organization_id', $orgId)
			->where('user_id', $user->id)
			->where('is_read', 0)
			->count();

		// ✅ counts for tabs
		$allCount = Notification::where('organization_id', $orgId)
			->where('user_id', $user->id)
			->count();

		$messageCount = Notification::where('organization_id', $orgId)
			->where('user_id', $user->id)
			->where('type', 'message')
			->count();

		$alertCount = Notification::where('organization_id', $orgId)
			->where('user_id', $user->id)
			->where('type', 'alert')
			->count();

		return response()->json([
			'status' => true,
			'unread' => $unreadCount,
			'counts' => [
				'all'     => $allCount,
				'message' => $messageCount,
				'alert'   => $alertCount,
			],
			'items'  => $items,
		]);
	}

    // mark single read
    public function markRead($id)
    {
        $user = Sentinel::check();
        $orgId = $this->getOrgId();

        $n = Notification::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $n->is_read = 1;
        $n->read_at = now();
        $n->save();

        return response()->json(['status' => true]);
    }

    // mark multiple read
    public function markBulkRead(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        $user = Sentinel::check();
        $orgId = $this->getOrgId();

        Notification::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->whereIn('id', $request->ids)
            ->update([
                'is_read' => 1,
                'read_at' => now()
            ]);

        return response()->json(['status' => true]);
    }

    // mark all read
    public function markAllRead()
    {
        $user = Sentinel::check();
        $orgId = $this->getOrgId();

        Notification::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => now()
            ]);

        return response()->json(['status' => true]);
    }
}
