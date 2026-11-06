<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $leads = Lead::query()
            ->with('visitor:id,city,country,last_source')
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('ref', $search)))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.leads.index', ['leads' => $leads, 'search' => $search]);
    }

    public function show(Lead $lead): View
    {
        return view('admin.leads.show', ['lead' => $lead, 'presenter' => $lead->presenter()]);
    }
}
