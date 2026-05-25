<div>
    @foreach ($items as $item)
    <div>
        <span class="badge bg-primary me-2">{{ $item->name }}</span>
        <a href="{{ route('items.edit', $item) }}" class="button is-info">Edit</a>

        <div>
            <form action="{{ route('items.destroy', $item) }}" method="POST">
                @method('DELETE')
                @csrf
                <button class="button is-danger">Item löschen</button>
            </form>
        </div>
    </div>
    @endforeach
</div>