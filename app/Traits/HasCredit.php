<?php

namespace App\Traits;

use App\Models\Cost;
use App\Models\Credit;
use App\Models\CreditMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HasCredit
{
    /**
     * Get all credits for this company
     * 
     * @return HasMany
     */
    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    /**
     * Get active credits (not expired)
     * 
     * @return HasMany
     */
    public function activeCredits()
    {
        return $this->credits()
            ->where(function($query) {
                $query->whereNull('expiration_date')
                    ->orWhere('expiration_date', '>=', Carbon::now());
            })
            ->where('remaining_credit_amount', '>', 0)
            ->orderBy('expiration_date', 'asc'); // Use credits that expire soonest first
    }

    /**
     * Get total remaining credits across all active credit records
     * 
     * @return int
     */
    public function getTotalRemainingCredits()
    {
        return $this->activeCredits()->sum('remaining_credit_amount');
    }

    /**
     * Add credits to the company
     * 
     * @param int $amount
     * @param string|null $source
     * @param Carbon|null $expirationDate
     * @return Credit
     */
    public function addCredits(int $amount, ?string $source = null, ?Carbon $expirationDate = null)
    {
        return $this->credits()->create([
            'credit_amount' => $amount,
            'remaining_credit_amount' => $amount,
            'used_credit_amount' => 0,
            'source' => $source,
            'expiration_date' => $expirationDate
        ]);
    }

    /**
     * Check if there is enough credits to spend, by action name
     * 
     * @param string $action
     * @return bool
     */
    public function hasEnoughCreditsByAction(string $action): bool
    {
        //If we do have credits system disabled, return true
        if(config('settings.enable_credits', false) == false){
            return true;
        }
        $amount = Cost::where('action', $action)->first()->cost ?? 0;
        if($amount == -1){
            //When based on usage, we need at least 1 credit
           $amount = 1;
        }
        return $this->hasEnoughCredits($amount);
    }   

    /**
     * Check if there is enough credits to spend
     * 
     * @param int $amount
     * @return bool
     */
    public function hasEnoughCredits(int $amount): bool
    {
        //If we do have credits system disabled, return true
        if(config('settings.enable_credits', false) == false){
            return true;
        }

        return $this->getTotalRemainingCredits() >= $amount;
    }

    //useCredits by action name
    public function useCreditsByAction(string $action, int $amountBasedOnUsage = 1): bool
    {
        //If we do have credits system disabled, return true
        if(config('settings.enable_credits', false) == false){
            return true;
        }
        $cost = Cost::where('action', $action)->first() ?? null;
        $amount = $cost ? $cost->cost : 0;
        if($amount == -1){
            //When based on usage, we need the usage of the action
           $amount = $amountBasedOnUsage;
        }
        return $this->useCredits($amount, $action);
    }

    /**
     * Use credits for an action
     * Returns true if successful, false if insufficient credits
     */
    public function useCredits(int $amount, string $action): bool
    {
        //If we do have credits system disabled, return true
        if(config('settings.enable_credits', false) == false){
            return true;
        }

        if ($this->getTotalRemainingCredits() < $amount) {
            return false;
        }

        $remainingToSpend = $amount;
        
        foreach ($this->activeCredits()->get() as $credit) {
            $spendFromThis = min($remainingToSpend, $credit->remaining_credit_amount);
            
            $credit->used_credit_amount += $spendFromThis;
            $credit->remaining_credit_amount -= $spendFromThis;
            $credit->save();

            $data=[
                'credit_id' => $credit->id,
                'action' => $action,
                'amount' => $spendFromThis,
                'company_id' => $this->id
            ];
            Log::info("useCredits",[$data]);

            //Add a movement log
            CreditMovement::create($data);

            $remainingToSpend -= $spendFromThis;
            
            if ($remainingToSpend <= 0) {
                break;
            }
        }

        return true;
    }


    /**
     * Get the total remaining credits for the company and the percentage of credits used
     * 
     * @return array
     */
    public function getTotalRemainingCreditsAndPercentageUsed(): array
    {
        return [$this->getTotalRemainingCredits(), $this->getPercentageOfCreditsUsed()];
    }

    /**
     * Get the percentage of credits used
     * 
     * @return int
     */
    public function getPercentageOfCreditsUsed()
    {
       //This percentage is calculated based on the total credits that are not expired
       $totalCredits=$this->credits()->where('expiration_date', '>=', now())->sum('credit_amount');
       $usedCredits=$this->credits()->where('expiration_date', '>=', now())->sum('used_credit_amount');
       
       //Handle division by zero case
       $percentage = $totalCredits > 0 ? round(($usedCredits / $totalCredits) * 100) : 0;
       
       return [$percentage, $totalCredits, $usedCredits];
    }
}
