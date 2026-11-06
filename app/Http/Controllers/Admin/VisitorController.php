<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $onlyLeads = $request->boolean('leads');

        $visitors = Visitor::query()
            ->withCount('leads')
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('last_ip', 'like', "{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('country', 'like', "%{$search}%")
                ->orWhere('network', 'like', "%{$search}%")
                ->orWhere('last_source', 'like', "%{$search}%")))
            ->when($onlyLeads, fn ($query) => $query->has('leads'))
            ->latest('last_seen_at')
            ->paginate(50)
            ->withQueryString();

        return view('admin.visitors.index', ['visitors' => $visitors, 'search' => $search, 'onlyLeads' => $onlyLeads]);
    }

    public function show(Visitor $visitor): View
    {
        $visits = $visitor->visits()
            ->with(['events' => fn ($query) => $query->orderBy('id')])
            ->latest('started_at')
            ->limit(50)
            ->get();

        return view('admin.visitors.show', [
            'visitor' => $visitor,
            'visits' => $visits,
            'leads' => $visitor->leads()->latest()->get(),
        ]);
    }
}
