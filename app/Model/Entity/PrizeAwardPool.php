<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class PrizeAwardPool
 *
 * @since 2.0
 *
 * @Entity(table="prize_award_pool")
 */
class PrizeAwardPool extends Model
{
    /**
     * 通用奖池ID
     * @Id(incrementing=false)
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 库存
     *
     * @Column(name="in_stock", prop="inStock")
     *
     * @var int
     */
    private $inStock;

    /**
     * 最后一次领取时间
     *
     * @Column(name="last_time", prop="lastTime")
     *
     * @var string
     */
    private $lastTime;


    /**
     * @param int $id
     *
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int $inStock
     *
     * @return void
     */
    public function setInStock(int $inStock): void
    {
        $this->inStock = $inStock;
    }

    /**
     * @param string $lastTime
     *
     * @return void
     */
    public function setLastTime(string $lastTime): void
    {
        $this->lastTime = $lastTime;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getInStock(): ?int
    {
        return $this->inStock;
    }

    /**
     * @return string
     */
    public function getLastTime(): ?string
    {
        return $this->lastTime;
    }

}
