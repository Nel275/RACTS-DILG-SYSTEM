{{-- Stamp action with a confirmation naming the report; the button disables on submit to stop double-clicks (the server also refuses duplicates). --}}
@php($confirm = sprintf("Stamp this report?\n\n%s\n%s\n%s - submitted %s\n\nA stamped copy signed with your name (%s) will be created. The original file stays unchanged.",
    $entry->referenceNumber(), $entry->item_description, $entry->barangay->barangay_name,
    $entry->date_submitted->format('M d, Y g:i A'), auth()->user()->name))
<form action="{{ route('digital-stamping.stamp', $entry) }}" method="POST" class="d-inline"
      onsubmit="if (!confirm({{ \Illuminate\Support\Js::from($confirm) }})) return false; this.querySelector('button').disabled = true;">
    @csrf
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-patch-check"></i> Stamp Report</button>
</form>
