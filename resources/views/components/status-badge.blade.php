@if($status == 'active')
<span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">
    Active
</span>
@elseif($status == 'closed')
<span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">
    Closed
</span>
@else
<span class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded-full">
    Draft
</span>
@endif