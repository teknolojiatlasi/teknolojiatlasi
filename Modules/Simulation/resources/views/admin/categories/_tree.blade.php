@foreach ($categories as $category)
    @include('simulation::admin.categories._category_row', ['category' => $category, 'level' => 0])
@endforeach
