<?php

namespace App\Http\Controllers;

use App\Models\MenuItemCostHistory;
use Illuminate\Http\Request;

class MenuItemCostHistoryController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'menu_item_id' => 'required|integer|exists:menu_items,id',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = (int) ($data['limit'] ?? 30);

        $rows = MenuItemCostHistory::query()
            ->where('menu_item_id', $data['menu_item_id'])
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return response()->json($rows);
    }
}
