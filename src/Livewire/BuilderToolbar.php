<?php

namespace TomatoPHP\FilamentPageBuilder\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use TomatoPHP\FilamentPageBuilder\Facades\FilamentCMS;
use TomatoPHP\FilamentPageBuilder\Filament\Resources\PostResource\Pages\EditPost;
use TomatoPHP\FilamentPageBuilder\Models\Post;

class BuilderToolbar extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];

    public ?Post $page = null;

    public ?bool $loaded = false;

    public ?bool $preview = false;

    public function mount(): void
    {
        $this->form->fill([
            'sections' => $this->page->meta('sections') ?? [],
        ]);

        if (session()->has('preview_' . $this->page->id)) {
            $this->preview = session()->get('preview_' . $this->page->id);
        } else {
            session()->put('preview_' . $this->page->id, $this->preview);
        }
    }

    public function editPageAction()
    {
        $page = $this->page;

        return Action::make('editPage')
            ->label('Edit Page')
            ->action(function (array $data) use ($page) {
                return redirect()->to(EditPost::getUrl([$page->id]));
            });
    }

    public function previewPageAction()
    {
        $page = $this->page;

        return Action::make('previewPage')
            ->label($this->preview ? 'Show Builder' : 'Preview Page')
            ->action(function () use ($page) {
                $this->preview = ! $this->preview;
                session()->put('preview_' . $page->id, $this->preview);
            });
    }

    public function form(Form $form): Form
    {
        $sections = FilamentCMS::themes()->getSections();
        $blocks = [];
        foreach ($sections as $section) {
            $blocks[] = Builder\Block::make($section->key)
                ->schema($section->form);
        }

        return $form
            ->schema([
                Forms\Components\Builder::make('sections')
                    ->blocks($blocks)
                    ->collapsible()
                    ->cloneable()
                    ->required()
                    ->minItems(1)
                    ->columns(2),
            ])->statePath('data');
    }

    public function saveSections(): void
    {
        $sections = collect($this->form->getState()['sections'])->map(function ($item, $key) {
            $item['uuid'] = Str::uuid()->toString();
            $item['order'] = $key;

            return $item;
        });
        $this->page->meta('sections', $sections);

        $this->preview = ! $this->preview;
        session()->put('preview_' . $this->page->id, $this->preview);

        Notification::make()
            ->title('Section added')
            ->body('Section added successfully')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('filament-cms::themes.builder-toolbar-livewire');
    }
}
