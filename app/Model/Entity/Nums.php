<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class Nums
 *
 * @since 2.0
 *
 * @Entity(table="nums")
 */
class Nums extends Model
{
    /**
     * 
     * @Id(incrementing=false)
     * @Column()
     *
     * @var int
     */
    private $n;


    /**
     * @param int $n
     *
     * @return void
     */
    public function setN(int $n): void
    {
        $this->n = $n;
    }

    /**
     * @return int
     */
    public function getN(): ?int
    {
        return $this->n;
    }

}
