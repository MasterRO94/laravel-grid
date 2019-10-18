@php
	/** @var \MasterRO\Grid\Builders\Column$column*/
@endphp

<thead>
<tr>
	@foreach($columns as $column)
		<th>
			<span class="{{ $column->orderable ? 'sorting-arrow' : '' }}">{{ $column->title }}</span>
		</th>
	@endforeach
</tr>
</thead>
