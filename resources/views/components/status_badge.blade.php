<span class="badge {{ $row->status == 'paid' ? 'bg-success' : ($row->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
    {{ ucfirst($row->status) }}
</span>