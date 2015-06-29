<?php

use Eddieh\Monero\Payment;
use Eddieh\Monero\Monero;

class Funding extends \Eloquent
{
	protected $table = 'funding';

	protected $fillable = [
		'payment_id',
		'funded',
		'target',
		'currency',
		'thread_id',
		'id'
	];

	public function thread()
	{
		return $this->belongsTo('Thread');
	}

	public function milestones()
	{
		return $this->hasMany('Milestone');
	}

	public function payouts()
	{
		return $this->hasMany('Payout');
	}

	public function percentage()
	{
		$cache_key = $this->id . '_funding_';
		$percentage = Cache::tags('thread_' . $this->thread_id)->rememberForever($cache_key . 'percentage', function () {
			$value = ($this->funded() / $this->target) * 100;
			return $value;
		});
		return $percentage;
	}

	public function contributions()
	{
		$cache_key = $this->id . '_funding_';
		$contributions = Cache::tags('thread_' . $this->thread_id)->rememberForever($cache_key . 'contributions', function () {
			return Payment::where('payment_id', $this->payment_id)->where('amount', '<>', 0)->count();
		});
		return $contributions;
	}

	public function funded()
	{
		$cache_key = $this->id . '_funding_';
		$funded = Cache::tags('thread_' . $this->thread_id)->rememberForever($cache_key . 'funded', function () {
			$payments = Payment::where('payment_id', $this->payment_id);
			$_funded = $payments->where('type', 'receive')->sum('amount');
			$_funded = Monero::convert($_funded, $this->currency);
			return $_funded;
		});
		return $funded;
	}

	public function balance()
	{
		$cache_key = $this->id . '_funding_';
		$balance = Cache::tags('thread_' . $this->thread_id)->remember($cache_key . 'balance', 0.3, function () {
			$funded = $this->funded();
			$currency = $this->currency;
			$payouts = $this->payouts()->sum('amount');

			if ($currency != 'XMR') {
				$_payouts = Monero::convert($payouts * 1000000000000, $currency);
				$balance = $funded - $_payouts;
			} else {
				$balance = $funded - $payouts;
			}

			return $balance;
		});

		return $balance;
	}

	public function balancePercentage()
	{
		$cache_key = $this->id . '_funding_';
		$balance = Cache::tags('thread_' . $this->thread_id)->remember($cache_key . 'balancePercentage', 0.3, function () {
			$balance = $this->balance();
			$funded = $this->funded();
			$used = $funded - $balance;
			if($funded) {
				$percentage = (100 * $used) / $funded;
				return $percentage;
			}
			else
			{
				return 0;
			}
		});
		return $balance;
	}

	//returns the currency symbol
	public function symbol()
	{
		switch ($this->currency) {
			case 'USD':
				return '$';
			case 'GBP':
				return '£';
			case 'EUR':
				return '€';
			default :
				return $this->currency;
		}
	}
}