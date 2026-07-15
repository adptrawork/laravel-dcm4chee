<?php

use App\Models\Order;
use App\Models\WorklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/worklist/status', function (Request $request) {
    $key = $request->header('X-Api-Key');
    if (! $key || $key !== config('app.worklist_api_key')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $item = WorklistItem::where('accession_number', $request->input('accession_number'))->first();
    if (! $item) {
        return response()->json(['error' => 'Not found'], 404);
    }

    $status = $request->input('status');
    $valid = [WorklistItem::STATUS_TAKEN_BY_MODALITY, WorklistItem::STATUS_ACQUIRING, WorklistItem::STATUS_ACQUIRED, WorklistItem::STATUS_SENT_TO_PACS];
    if (! in_array($status, $valid)) {
        return response()->json(['error' => 'Invalid status'], 422);
    }

    $data = ['status' => $status];
    if ($studyUid = $request->input('study_instance_uid')) {
        $data['study_instance_uid'] = $studyUid;
    }
    if ($status === WorklistItem::STATUS_TAKEN_BY_MODALITY) {
        $data['taken_at'] = now();
    }
    if ($status === WorklistItem::STATUS_ACQUIRED) {
        $data['acquired_at'] = now();
    }
    if ($status === WorklistItem::STATUS_SENT_TO_PACS) {
        $data['sent_at'] = now();
    }

    $item->update($data);

    if ($status === WorklistItem::STATUS_SENT_TO_PACS && $item->order) {
        $from = $item->order->status;
        $item->order->updateQuietly(['status' => Order::STATUS_COMPLETED]);
        Order::logStatus($item->order, Order::STATUS_COMPLETED, 'Study received by PACS', $from);
    }

    if ($status === WorklistItem::STATUS_TAKEN_BY_MODALITY && $item->order) {
        $from = $item->order->status;
        $item->order->updateQuietly(['status' => Order::STATUS_IN_PROGRESS]);
        Order::logStatus($item->order, Order::STATUS_IN_PROGRESS, 'Modality started exam', $from);
    }

    return response()->json(['ok' => true]);
});
