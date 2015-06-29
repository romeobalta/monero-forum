<div class="funding-wrapper">
	<div class="row funding-block">
		<h3>{{ $thread->funding->symbol() }}{{ number_format ($thread->funding->funded(), 2, ".", ",") }}
		</h3>
		<p>funded of {{ $thread->funding->symbol() }}{{ number_format($thread->funding->target, 2, ".", ",") }} target</p>
	</div>
	<div class="row the-bar">
		<div class="col-xs-6">
			{{ $thread->funding->contributions() }} individual contributions
		</div>
		<div class="col-xs-6 text-right">
			{{ number_format($thread->funding->percentage(), 2) }}%
		</div>
		<div class="col-lg-12">
			<div class="progress">
				<div class="progress-bar progress-monero progress-bar-striped" style="width: {{ $thread->funding->percentage() }}%;">
					<span class="sr-only">{{ $thread->funding->percentage() }}% Funded</span>
				</div>
			</div>
		</div>
		<div class="col-xs-6">
			{{ $thread->funding->payouts->count() }} payouts
		</div>
		<div class="col-xs-6 text-right">
			{{ $thread->funding->symbol() }}{{ number_format($thread->funding->balance(), 2) }} balance available
		</div>
		<div class="col-lg-12">
			<div class="progress">
				<div class="progress-bar progress-warning progress-bar" style="width: {{ $thread->funding->balancePercentage() }}%;">
					<span class="sr-only">{{ $thread->funding->balancePercentage() }}% Paid Out</span>
				</div>
			</div>
		</div>
		<div class="col-lg-12 text-center">
			<a href="{{ URL::route('contribute', $thread->id) }}"><button class="btn btn-success btn-lg">Contribute</button></a>
			@if(Auth::user() && Auth::user()->can('admin_panel'))
			<a href="{{ URL::route('milestones.index', $thread->id) }}"><button class="btn btn-danger btn-lg">Edit Milestones</button></a>
			@endif
		</div>
		<div class="col-lg-12">
			@if($thread->funding->payouts)
				<h2>Payouts</h2>
				<ul class="fa-ul">
				@foreach($thread->funding->payouts as $payout)
					<li>
						<i class="fa-li fa fa-circle-thin"></i>
						{{ $payout->amount }} XMR ({{ $payout->created_at->formatLocalized('%A %d %B %Y') }})
					</li>
				@endforeach
				</ul>
			@endif
			<h2>Milestones</h2>
			<ul class="fa-ul">
				@foreach($thread->funding->milestones as $milestone)
					<li>
						@if($milestone->complete)
							<h4><i class="fa-li fa fa-check-square-o"></i>{{{ $milestone->title }}}</h4>
						@else
							<h4><i class="fa-li fa fa-square-o"></i>{{{ $milestone->title }}}</h4>
						@endif
						@if($milestone->description)
							<p>{{{ $milestone->description }}}</p>
						@endif
					</li>
				@endforeach
			</ul>
		</div>
	</div>
	{{--@foreach(\Eddieh\Monero\Payment::where('payment_id', $thread->funding->payment_id)->get() as $backer)--}}
	{{--XMR -> {{ \Eddieh\Monero\Monero::convert($backer->amount, 'XMR') }} USD -> {{ \Eddieh\Monero\Monero::convert($backer->amount, 'USD') }}<br/>--}}
	{{--@endforeach--}}
</div>