<x-filament-panels::page>
    @if (method_exists($this, 'filters'))
        {{ $this->filters }}
    @endif

    <x-filament-widgets::widgets
        :columns="$this->getHeaderWidgetsColumns()"
        :data="$this->getWidgetData()"
        :widgets="$this->getDashboardWidgets()"
    />
</x-filament-panels::page>
