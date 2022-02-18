<?php

namespace App\Jobs;

use App\Models\Coin;
use App\Models\UserCoin;
use App\Models\UserWallet;
use Database\Seeders\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class WalletCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $try;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $try)
    {
        $this->user = (object)$user;
        $this->try = $try;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if ($this->try > 3) {
            return;
        }
        $problem = false;
        $network = \App\Models\Network::get()->groupBy('short_name');
        $userCoins = UserCoin::where('users_id', $this->user->id)->get()->pluck('coins_id');
        $coins = Coin::whereNotIn('id', $userCoins)->get()->groupBy('networks_id');
        try {
            // Create BSC Wallet and Coins
            if (empty($bscInsertWallet = UserWallet::where('users_id', $this->user->id)->where('networks_id', $network['BSC']->first()->id)->first())) {
                if (!empty($ethCheckWallet = UserWallet::where('users_id', $this->user->id)->where('networks_id', $network['ETH']->first()->id)->first())) {
                    $bscInsertWallet = UserWallet::create([
                        'users_id' => $this->user->id,
                        'networks_id' => $network['BSC']->first()->id,
                        'wallet' => $ethCheckWallet->wallet,
                        'password' => $ethCheckWallet->password,
                    ]);
                } else if (($wallet = bscActions("create_wallet")) && $wallet->status) {
                    $bscInsertWallet = UserWallet::create([
                        'users_id' => $this->user->id,
                        'networks_id' => $network['BSC']->first()->id,
                        'wallet' => $wallet->content->adress,
                        'password' => $wallet->content->private_key,
                    ]);
                } else if (!$wallet->status) {
                    $problem = true;
                }
            }
            if (isset($coins[$network['BSC']->first()->id])) {
                foreach ($coins[$network['BSC']->first()->id] as $coin) {
                    UserCoin::create([
                        'users_id' => $this->user->id,
                        'user_wallets_id' => $bscInsertWallet->id,
                        'coins_id' => $coin->id,
                        'balance_pure' => 0,
                        'balance' => 0,
                    ]);
                }
            }
            // Create ETH Wallet and Coins
            if (empty($ethInsertWallet = UserWallet::where('users_id', $this->user->id)->where('networks_id', $network['ETH']->first()->id)->first())) {
                if (!empty($bscCheckWallet = UserWallet::where('users_id', $this->user->id)->where('networks_id', $network['BSC']->first()->id)->first())) {
                    $ethInsertWallet = UserWallet::create([
                        'users_id' => $this->user->id,
                        'networks_id' => $network['ETH']->first()->id,
                        'wallet' => $bscCheckWallet->wallet,
                        'password' => $bscCheckWallet->password,
                    ]);
                } else if (($wallet = bscActions("create_wallet")) && $wallet->status) {
                    $ethInsertWallet = UserWallet::create([
                        'users_id' => $this->user->id,
                        'networks_id' => $network['ETH']->first()->id,
                        'wallet' => $wallet->content->adress,
                        'password' => $wallet->content->private_key,
                    ]);
                } else if (!$wallet->status) {
                    $problem = true;
                }
            }
            if (isset($coins[$network['ETH']->first()->id])) {
                foreach ($coins[$network['ETH']->first()->id] as $coin) {
                    UserCoin::create([
                        'users_id' => $this->user->id,
                        'user_wallets_id' => $ethInsertWallet->id,
                        'coins_id' => $coin->id,
                        'balance_pure' => 0,
                        'balance' => 0,
                    ]);
                }
            }

            // Create DXC Wallet and Coins
            if (empty($dxcInsertWallet = UserWallet::where('users_id', $this->user->id)->where('networks_id', $network['DXC']->first()->id)->first())) {
                if (($wallet = dxcActions("create_wallet", ['password' => Str::uuid()->toString()])) && $wallet->status) {
                    $dxcInsertWallet = UserWallet::create([
                        'users_id' => $this->user->id,
                        'networks_id' => $network['DXC']->first()->id,
                        'wallet' => $wallet->content->Address,
                        'password' => $wallet->content->password,
                    ]);
                } else if (!$wallet->status) {
                    $problem = true;
                }
            }
            if (isset($coins[$network['DXC']->first()->id])) {
                foreach ($coins[$network['DXC']->first()->id] as $coin) {
                    UserCoin::create([
                        'users_id' => $this->user->id,
                        'user_wallets_id' => $dxcInsertWallet->id,
                        'coins_id' => $coin->id,
                        'balance_pure' => 0,
                        'balance' => 0,
                    ]);
                }
            }

            if (empty($sourceInsertWallet = UserWallet::where('users_id', $this->user->id)->where('networks_id', $network['SOURCE']->first()->id)->first())) {
                $sourceInsertWallet = UserWallet::create([
                    'users_id' => $this->user->id,
                    'networks_id' => $network['SOURCE']->first()->id,
                    'wallet' => Uuid::uuid4(),
                    'password' => Uuid::uuid4(),
                ]);
            }
            if (isset($coins[$network['SOURCE']->first()->id])) {
                foreach ($coins[$network['SOURCE']->first()->id] as $coin) {
                    UserCoin::create([
                        'users_id' => $this->user->id,
                        'user_wallets_id' => $sourceInsertWallet->id,
                        'coins_id' => $coin->id,
                        'balance_pure' => 0,
                        'balance' => 0,
                    ]);
                }
            }
        } catch (\Exception $e) {
            $problem = true;
        }


        if ($problem) {
            WalletCreate::dispatch((array)$this->user, ($this->try + 1))->delay(now()->addMinutes(15));
        }
        return true;
    }
}
