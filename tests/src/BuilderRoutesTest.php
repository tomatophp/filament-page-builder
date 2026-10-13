<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use TomatoPHP\FilamentPageBuilder\Tests\Models\Page;
use TomatoPHP\FilamentPageBuilder\Tests\Models\User;
use TomatoPHP\FilamentPageBuilder\Tests\Policies\OwnerPagePolicy;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

beforeEach(function () {
    Schema::dropIfExists('builder_test_pages');
    Schema::create('builder_test_pages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable();
        $table->foreignId('team_id')->nullable();
        $table->json('meta')->nullable();
        $table->timestamps();
    });

    if (! Schema::hasTable('users')) {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    config()->set('filament-page-builder.model', Page::class);

    $this->owner = User::factory()->create();
    $this->other = User::factory()->create();

    $this->page = Page::create([
        'user_id' => $this->owner->id,
        'meta' => ['sections' => [
            ['uuid' => 'first', 'key' => 'hero'],
            ['uuid' => 'second', 'key' => 'footer'],
        ]],
    ]);
});

afterEach(function () {
    Schema::dropIfExists('builder_test_pages');
});

function withPagePolicy(): void
{
    Gate::policy(Page::class, OwnerPagePolicy::class);
}

it('rejects guests', function () {
    withPagePolicy();

    postJson(route('admin.pages.clear', $this->page))->assertUnauthorized();

    expect($this->page->fresh()->meta('sections'))->toHaveCount(2);
});

it('denies every request when the host registers no policy', function () {
    actingAs($this->owner);

    postJson(route('admin.pages.clear', $this->page))->assertForbidden();
    postJson(route('admin.pages.remove', $this->page), ['section' => 'first'])->assertForbidden();
    postJson(route('admin.pages.sections', $this->page), ['section' => 'hero'])->assertForbidden();
    postJson(route('admin.pages.meta.store', $this->page), ['section' => 'first'])->assertForbidden();
    get(route('admin.pages.meta', ['model' => $this->page, 'section' => 'first']))->assertForbidden();
    get(route('admin.pages.builder', $this->page))->assertForbidden();

    expect($this->page->fresh()->meta('sections'))->toHaveCount(2);
});

it('does not let another user change a page', function () {
    withPagePolicy();
    actingAs($this->other);

    postJson(route('admin.pages.clear', $this->page))->assertForbidden();
    postJson(route('admin.pages.remove', $this->page), ['section' => 'first'])->assertForbidden();
    postJson(route('admin.pages.meta.store', $this->page), ['section' => 'first'])->assertForbidden();
    get(route('admin.pages.builder', $this->page))->assertForbidden();

    expect($this->page->fresh()->meta('sections'))->toHaveCount(2);
});

it('lets the page owner clear sections', function () {
    withPagePolicy();
    actingAs($this->owner);

    postJson(route('admin.pages.clear', $this->page))->assertRedirect();

    expect($this->page->fresh()->meta('sections'))->toBe([]);
});

it('lets the page owner remove a section', function () {
    withPagePolicy();
    actingAs($this->owner);

    postJson(route('admin.pages.remove', $this->page), ['section' => 'first'])->assertRedirect();

    expect(collect($this->page->fresh()->meta('sections'))->pluck('uuid')->all())->toBe(['second']);
});

it('does not change pages over GET', function () {
    withPagePolicy();
    actingAs($this->owner);

    get('admin/pages/' . $this->page->id . '/clear')->assertStatus(405);
    get('admin/pages/' . $this->page->id . '/sections/remove?section=first')->assertStatus(405);

    expect($this->page->fresh()->meta('sections'))->toHaveCount(2);
});

it('denies team pages when the user cannot access the team', function () {
    withPagePolicy();
    $this->page->update(['team_id' => 99]);
    actingAs($this->owner);

    postJson(route('admin.pages.clear', $this->page))->assertForbidden();

    expect($this->page->fresh()->meta('sections'))->toHaveCount(2);
});

it('returns 404 for unknown pages', function () {
    withPagePolicy();
    actingAs($this->owner);

    postJson(route('admin.pages.clear', ['model' => 999]))->assertNotFound();
});

it('returns 404 when the configured model does not exist', function () {
    withPagePolicy();
    config()->set('filament-page-builder.model', 'App\Models\Missing');
    actingAs($this->owner);

    postJson(route('admin.pages.clear', $this->page))->assertNotFound();
});
