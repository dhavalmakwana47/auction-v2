<?php

namespace App\Events;

use App\Models\AuctionBid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public AuctionBid $bid) {}

    public function broadcastOn(): Channel
    {
        return new Channel('auction.' . $this->bid->auction_id);
    }

    public function broadcastAs(): string
    {
        return 'bid.placed';
    }

    public function broadcastWith(): array
    {
        $auction    = $this->bid->auction;
        $highestBid = max(
            (float) str_replace(',', '', $auction->base_price),
            $auction->bids()->where('status', 'confirmed')->max('bid_amount') ?? 0
        );
        $incrementAmount = (float) str_replace(',', '', $auction->increment_amount);

        return [
            'bid_id'       => $this->bid->id,
            'ra_name'      => $this->bid->user->name,
            'bid_amount'   => (float) $this->bid->bid_amount,
            'total_npv'    => (float) $this->bid->total_npv,
            'placed_at'    => $this->bid->created_at->format('d M Y, h:i A'),
            'highest_bid'  => (float) $highestBid,
            'minimum_next' => (float) ($highestBid + $incrementAmount),
            'total_bids'   => $auction->bids()->count(),
            'valid_bids'   => $auction->bids()->where('status', 'confirmed')->count(),
        ];
    }
}
