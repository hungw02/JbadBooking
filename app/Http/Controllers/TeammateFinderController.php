<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeammateFinder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
class TeammateFinderController extends BaseController
{
    public function index(Request $request)
    {
        $query = TeammateFinder::where('is_active', true);
        
        if ($request->has('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('skill_level') && $request->skill_level != 'all') {
            $query->where('skill_level', $request->skill_level);
        }
        
        $teammates = $query->latest()->paginate(10);
        
        return view('customer.teammate.teammate-finder', compact('teammates'));
    }
    
    public function create()
    {
        $exists = TeammateFinder::where('user_id', Auth::id())->where('is_active', true)->exists();
        
        if ($exists) {
            return redirect()->route('teammate.edit')->with('info', 'Bạn đã có hồ sơ tìm đồng đội. Bạn có thể chỉnh sửa hồ sơ của mình.');
        }
        
        return view('customer.teammate.teammate-create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'skill_level' => 'required|in:yếu,trung bình yếu,trung bình,trung bình khá,khá',
            'contact_info' => 'required|string|max:255',
            'expectations' => 'nullable|string',
            'play_time' => 'nullable|string|max:255',
        ]);
        
        TeammateFinder::create([
            'user_id' => Auth::id(),
            'full_name' => $request->full_name,
            'skill_level' => $request->skill_level,
            'contact_info' => $request->contact_info,
            'expectations' => $request->expectations,
            'play_time' => $request->play_time,
            'is_active' => true,
        ]);
        
        return redirect()->route('teammate.index')->with('success', 'Hồ sơ tìm đồng đội của bạn đã được tạo thành công.');
    }
    
    public function show($id)
    {
        $teammate = TeammateFinder::findOrFail($id);
        return view('customer.teammate.teammate-detail', compact('teammate'));
    }
    
    public function edit()
    {
        $teammate = TeammateFinder::where('user_id', Auth::id())->where('is_active', true)->firstOrFail();
        return view('customer.teammate.teammate-edit', compact('teammate'));
    }
    
    public function update(Request $request)
    {
        $teammate = TeammateFinder::where('user_id', Auth::id())->where('is_active', true)->firstOrFail();
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'skill_level' => 'required|in:yếu,trung bình yếu,trung bình,trung bình khá,khá',
            'contact_info' => 'required|string|max:255',
            'expectations' => 'nullable|string',
            'play_time' => 'nullable|string|max:255',
        ]);
        
        $teammate->update([
            'full_name' => $request->full_name,
            'skill_level' => $request->skill_level,
            'contact_info' => $request->contact_info,
            'expectations' => $request->expectations,
            'play_time' => $request->play_time,
        ]);
        
        return redirect()->route('teammate.index')->with('success', 'Hồ sơ tìm đồng đội của bạn đã được cập nhật thành công.');
    }
    
    public function delete()
    {
        $teammate = TeammateFinder::where('user_id', Auth::id())->where('is_active', true)->firstOrFail();
        $teammate->update(['is_active' => false]);
        
        return redirect()->route('teammate.index')->with('success', 'Hồ sơ tìm đồng đội của bạn đã được xóa.');
    }
}
