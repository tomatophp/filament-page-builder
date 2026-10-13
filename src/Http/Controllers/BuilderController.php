<?php

namespace TomatoPHP\FilamentPageBuilder\Http\Controllers;

use Filament\Facades\Filament;
use Filament\Models\Contracts\HasTenants;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use TomatoPHP\FilamentPageBuilder\Facades\FilamentPageBuilder;
use TomatoPHP\TomatoThemes\Facades\TomatoThemes;

class BuilderController extends Controller
{
    public function builder(Request $request, string $model): View | JsonResponse
    {
        $model = $this->resolvePage($request, $model);

        $sections = FilamentPageBuilder::themes()->getSections()->groupBy('group');

        return view('filament-themes::pages.builder', compact('model', 'sections'));
    }

    public function meta(Request $request, string $model)
    {
        $model = $this->resolvePage($request, $model);

        $request->validate([
            'section' => 'required|string',
        ]);

        $sectionID = $request->get('section');
        $section = collect($model->meta('sections'))->where('uuid', $sectionID)->first();
        if ($section) {
            if (! empty($section['form'])) {
                return view('filament-themes::pages.meta', compact('model', 'section', 'sectionID'));
            }

            return $this->back(__('Section do not have form'), false);
        }

        return $this->back(__('Section not found'), false);
    }

    public function metaStore(Request $request, string $model)
    {
        $model = $this->resolvePage($request, $model);

        $request->validate([
            'section' => 'required|string',
        ]);

        $data = $request->all();
        $sectionID = $request->get('section');
        $section = collect($model->meta('sections'))->where('uuid', $sectionID)->first();

        if (! $section) {
            return $this->back(__('Section not found'), false);
        }

        if (isset($data['image'])) {
            $image = $data['image']->storeAs('public/sections', time() . '.' . $request->file('image')->extension());
            $data['image'] = url(Str::replace('public', 'storage', $image));
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $images = [];
            foreach ($data['images'] as $image) {
                $image = $image->storeAs('public/sections', time() . '.' . $image->extension());
                $images[] = url(Str::replace('public', 'storage', $image));
            }

            $data['images'] = $images;
        }

        $model->meta($sectionID, $data);

        return $this->back(__('Section updated successfully'));
    }

    public function remove(Request $request, string $model)
    {
        $model = $this->resolvePage($request, $model);

        $request->validate([
            'section' => 'required|string',
        ]);

        $section = $request->get('section');
        $sections = collect($model->meta('sections'))->filter(function ($item) use ($section) {
            return $item['uuid'] !== $section;
        })->values()->all();

        $model->meta('sections', $sections);

        return $this->back(__('Section removed successfully'));
    }

    public function sections(Request $request, string $model)
    {
        $model = $this->resolvePage($request, $model);

        $request->validate([
            'section' => 'required|string',
        ]);

        $section = TomatoThemes::find($request->get('section'));
        if (! $section) {
            return $this->back(__('Section not found'), false);
        }

        $sections = $model->meta('sections');
        $section['order'] = 0;
        $section['uuid'] = Str::uuid();
        $sections[] = $section;
        $model->meta('sections', $sections);

        return $this->back(__('Section added successfully'));
    }

    public function clear(Request $request, string $model)
    {
        $model = $this->resolvePage($request, $model);

        $model->meta('sections', []);

        return $this->back(__('Sections cleared successfully'));
    }

    /**
     * Load the page and make sure the current user may edit it.
     *
     * Uses the host application's policy for the page model (`update`).
     * Without a registered policy every request is denied.
     */
    protected function resolvePage(Request $request, string $key): Model
    {
        $user = $request->user();
        abort_unless($user, 403);

        $class = config('filament-page-builder.model');
        abort_unless(is_string($class) && class_exists($class) && is_subclass_of($class, Model::class), 404);

        /** @var Model $page */
        $page = $class::query()->findOrFail($key);

        $this->authorizeTenant($user, $page);

        abort_if(Gate::getPolicyFor($page) === null, 403);
        Gate::forUser($user)->authorize('update', $page);

        return $page;
    }

    /**
     * Pages that belong to a team can only be edited by users who can access that team.
     */
    protected function authorizeTenant($user, Model $page): void
    {
        $teamId = $page->getAttribute('team_id');
        if (blank($teamId)) {
            return;
        }

        abort_unless($user instanceof HasTenants, 403);

        foreach (Filament::getPanels() as $panel) {
            if (! $panel->hasTenancy()) {
                continue;
            }

            $tenant = $panel->getTenantModel()::query()->find($teamId);
            if ($tenant && $user->canAccessTenant($tenant)) {
                return;
            }
        }

        abort(403);
    }

    protected function back(string $message, bool $success = true): RedirectResponse
    {
        $notification = Notification::make()->title($message);
        ($success ? $notification->success() : $notification->danger())->send();

        return redirect()->back();
    }
}
