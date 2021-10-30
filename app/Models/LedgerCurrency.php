<?php

namespace App\Models;

use App\Exceptions\Breaker;
use App\Helpers\Revision;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Currencies available to the ledger.
 *
 * @property Carbon $created_at When the record was created.
 * @property string $code The currency code.
 * @property int $decimals The number of decimals to carry.
 * @property Carbon $revision Revision timestamp to detect race condition on update.
 * @property Carbon $updated_at When the record was updated.
 * @mixin Builder
 */
class LedgerCurrency extends Model
{
    use HasFactory;

    protected $dateFormat = 'Y-m-d H:i:s.u';
    protected $fillable = ['code', 'decimals'];
    public $incrementing = false;
    protected $keyType = 'string';

    public $primaryKey = 'code';

    /**
     * @param ?string $revision
     * @throws Breaker
     */
    public function checkRevision(?string $revision)
    {
        if ($revision !== Revision::create($this->revision, $this->updated_at)) {
            throw Breaker::fromCode(Breaker::BAD_REVISION);
        }
    }

    /**
     * @param array $options
     * @return array
     */
    public function toResponse(array $options = []): array
    {
        $response = [
            'code' => $this->code,
            'decimals' =>$this->decimals,
        ];
        $response['revision'] = Revision::create($this->revision, $this->updated_at);
        $response['createdAt'] = $this->created_at;
        $response['updatedAt'] = $this->updated_at;

        return $response;
    }

}
