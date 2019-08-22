<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class Account
 *
 * @since 2.0
 *
 * @Entity(table="account")
 */
class Account extends Model
{
    /**
     * 
     * @Id(incrementing=false)
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 
     *
     * @Column()
     *
     * @var string
     */
    private $nickname;

    /**
     * 钻石
     *
     * @Column()
     *
     * @var int
     */
    private $yuanbao;

    /**
     * 金币
     *
     * @Column()
     *
     * @var int
     */
    private $gold;

    /**
     * 赢局
     *
     * @Column()
     *
     * @var int
     */
    private $win;

    /**
     * 输局
     *
     * @Column()
     *
     * @var int
     */
    private $lose;

    /**
     * 平局
     *
     * @Column()
     *
     * @var int
     */
    private $draw;

    /**
     * 逃跑
     *
     * @Column()
     *
     * @var int
     */
    private $escape;

    /**
     * 最后一局结果，1输 2赢 3平 4逃跑
     *
     * @Column(name="last_versus", prop="lastVersus")
     *
     * @var int
     */
    private $lastVersus;

    /**
     * 当前连胜局数
     *
     * @Column(name="continue_win", prop="continueWin")
     *
     * @var int
     */
    private $continueWin;

    /**
     * 最高连胜局数
     *
     * @Column(name="highest_win", prop="highestWin")
     *
     * @var int
     */
    private $highestWin;

    /**
     * 性别,1男2女
     *
     * @Column()
     *
     * @var int
     */
    private $gender;

    /**
     * 在线时长(秒)
     *
     * @Column()
     *
     * @var int
     */
    private $duration;

    /**
     * 冠军次数
     *
     * @Column(name="first_prize", prop="firstPrize")
     *
     * @var int
     */
    private $firstPrize;

    /**
     * 亚军次数
     *
     * @Column(name="second_prize", prop="secondPrize")
     *
     * @var int
     */
    private $secondPrize;

    /**
     * 季军次数
     *
     * @Column(name="third_prize", prop="thirdPrize")
     *
     * @var int
     */
    private $thirdPrize;

    /**
     * 省份
     *
     * @Column()
     *
     * @var string
     */
    private $province;

    /**
     * 城市
     *
     * @Column()
     *
     * @var string
     */
    private $city;

    /**
     * 头像
     *
     * @Column()
     *
     * @var string
     */
    private $avatar;

    /**
     * 
     *
     * @Column(name="last_update_time", prop="lastUpdateTime")
     *
     * @var string
     */
    private $lastUpdateTime;

    /**
     * 闯关挑战赛——第一关勋章数
     *
     * @Column(name="lvl_score1", prop="lvlScore1")
     *
     * @var int
     */
    private $lvlScore1;

    /**
     * 闯关挑战赛——第二关勋章数
     *
     * @Column(name="lvl_score2", prop="lvlScore2")
     *
     * @var int
     */
    private $lvlScore2;

    /**
     * 闯关挑战赛——第三关勋章数
     *
     * @Column(name="lvl_score3", prop="lvlScore3")
     *
     * @var int
     */
    private $lvlScore3;

    /**
     * 闯关挑战赛——第四关勋章数
     *
     * @Column(name="lvl_score4", prop="lvlScore4")
     *
     * @var int
     */
    private $lvlScore4;

    /**
     * 闯关挑战赛——第五关勋章数
     *
     * @Column(name="lvl_score5", prop="lvlScore5")
     *
     * @var int
     */
    private $lvlScore5;

    /**
     * 闯关挑战赛——第六关勋章数
     *
     * @Column(name="lvl_score6", prop="lvlScore6")
     *
     * @var int
     */
    private $lvlScore6;

    /**
     * 金币场段位积分
     *
     * @Column(name="gold_score", prop="goldScore")
     *
     * @var int
     */
    private $goldScore;

    /**
     * 最大倍数
     *
     * @Column()
     *
     * @var int
     */
    private $times;

    /**
     * 炸弹数
     *
     * @Column()
     *
     * @var int
     */
    private $bomb;

    /**
     * 连炸数
     *
     * @Column(name="even_frying", prop="evenFrying")
     *
     * @var int
     */
    private $evenFrying;

    /**
     * 春天数
     *
     * @Column()
     *
     * @var int
     */
    private $spring;

    /**
     * 累计赢豆数
     *
     * @Column(name="win_gold", prop="winGold")
     *
     * @var int
     */
    private $winGold;


    /**
     * @param int $uid
     *
     * @return void
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @param string $nickname
     *
     * @return void
     */
    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * @param int $yuanbao
     *
     * @return void
     */
    public function setYuanbao(int $yuanbao): void
    {
        $this->yuanbao = $yuanbao;
    }

    /**
     * @param int $gold
     *
     * @return void
     */
    public function setGold(int $gold): void
    {
        $this->gold = $gold;
    }

    /**
     * @param int $win
     *
     * @return void
     */
    public function setWin(int $win): void
    {
        $this->win = $win;
    }

    /**
     * @param int $lose
     *
     * @return void
     */
    public function setLose(int $lose): void
    {
        $this->lose = $lose;
    }

    /**
     * @param int $draw
     *
     * @return void
     */
    public function setDraw(int $draw): void
    {
        $this->draw = $draw;
    }

    /**
     * @param int $escape
     *
     * @return void
     */
    public function setEscape(int $escape): void
    {
        $this->escape = $escape;
    }

    /**
     * @param int $lastVersus
     *
     * @return void
     */
    public function setLastVersus(int $lastVersus): void
    {
        $this->lastVersus = $lastVersus;
    }

    /**
     * @param int $continueWin
     *
     * @return void
     */
    public function setContinueWin(int $continueWin): void
    {
        $this->continueWin = $continueWin;
    }

    /**
     * @param int $highestWin
     *
     * @return void
     */
    public function setHighestWin(int $highestWin): void
    {
        $this->highestWin = $highestWin;
    }

    /**
     * @param int $gender
     *
     * @return void
     */
    public function setGender(int $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @param int $duration
     *
     * @return void
     */
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @param int $firstPrize
     *
     * @return void
     */
    public function setFirstPrize(int $firstPrize): void
    {
        $this->firstPrize = $firstPrize;
    }

    /**
     * @param int $secondPrize
     *
     * @return void
     */
    public function setSecondPrize(int $secondPrize): void
    {
        $this->secondPrize = $secondPrize;
    }

    /**
     * @param int $thirdPrize
     *
     * @return void
     */
    public function setThirdPrize(int $thirdPrize): void
    {
        $this->thirdPrize = $thirdPrize;
    }

    /**
     * @param string $province
     *
     * @return void
     */
    public function setProvince(string $province): void
    {
        $this->province = $province;
    }

    /**
     * @param string $city
     *
     * @return void
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @param string $avatar
     *
     * @return void
     */
    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @param string $lastUpdateTime
     *
     * @return void
     */
    public function setLastUpdateTime(string $lastUpdateTime): void
    {
        $this->lastUpdateTime = $lastUpdateTime;
    }

    /**
     * @param int $lvlScore1
     *
     * @return void
     */
    public function setLvlScore1(int $lvlScore1): void
    {
        $this->lvlScore1 = $lvlScore1;
    }

    /**
     * @param int $lvlScore2
     *
     * @return void
     */
    public function setLvlScore2(int $lvlScore2): void
    {
        $this->lvlScore2 = $lvlScore2;
    }

    /**
     * @param int $lvlScore3
     *
     * @return void
     */
    public function setLvlScore3(int $lvlScore3): void
    {
        $this->lvlScore3 = $lvlScore3;
    }

    /**
     * @param int $lvlScore4
     *
     * @return void
     */
    public function setLvlScore4(int $lvlScore4): void
    {
        $this->lvlScore4 = $lvlScore4;
    }

    /**
     * @param int $lvlScore5
     *
     * @return void
     */
    public function setLvlScore5(int $lvlScore5): void
    {
        $this->lvlScore5 = $lvlScore5;
    }

    /**
     * @param int $lvlScore6
     *
     * @return void
     */
    public function setLvlScore6(int $lvlScore6): void
    {
        $this->lvlScore6 = $lvlScore6;
    }

    /**
     * @param int $goldScore
     *
     * @return void
     */
    public function setGoldScore(int $goldScore): void
    {
        $this->goldScore = $goldScore;
    }

    /**
     * @param int $times
     *
     * @return void
     */
    public function setTimes(int $times): void
    {
        $this->times = $times;
    }

    /**
     * @param int $bomb
     *
     * @return void
     */
    public function setBomb(int $bomb): void
    {
        $this->bomb = $bomb;
    }

    /**
     * @param int $evenFrying
     *
     * @return void
     */
    public function setEvenFrying(int $evenFrying): void
    {
        $this->evenFrying = $evenFrying;
    }

    /**
     * @param int $spring
     *
     * @return void
     */
    public function setSpring(int $spring): void
    {
        $this->spring = $spring;
    }

    /**
     * @param int $winGold
     *
     * @return void
     */
    public function setWinGold(int $winGold): void
    {
        $this->winGold = $winGold;
    }

    /**
     * @return int
     */
    public function getUid(): ?int
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @return int
     */
    public function getYuanbao(): ?int
    {
        return $this->yuanbao;
    }

    /**
     * @return int
     */
    public function getGold(): ?int
    {
        return $this->gold;
    }

    /**
     * @return int
     */
    public function getWin(): ?int
    {
        return $this->win;
    }

    /**
     * @return int
     */
    public function getLose(): ?int
    {
        return $this->lose;
    }

    /**
     * @return int
     */
    public function getDraw(): ?int
    {
        return $this->draw;
    }

    /**
     * @return int
     */
    public function getEscape(): ?int
    {
        return $this->escape;
    }

    /**
     * @return int
     */
    public function getLastVersus(): ?int
    {
        return $this->lastVersus;
    }

    /**
     * @return int
     */
    public function getContinueWin(): ?int
    {
        return $this->continueWin;
    }

    /**
     * @return int
     */
    public function getHighestWin(): ?int
    {
        return $this->highestWin;
    }

    /**
     * @return int
     */
    public function getGender(): ?int
    {
        return $this->gender;
    }

    /**
     * @return int
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @return int
     */
    public function getFirstPrize(): ?int
    {
        return $this->firstPrize;
    }

    /**
     * @return int
     */
    public function getSecondPrize(): ?int
    {
        return $this->secondPrize;
    }

    /**
     * @return int
     */
    public function getThirdPrize(): ?int
    {
        return $this->thirdPrize;
    }

    /**
     * @return string
     */
    public function getProvince(): ?string
    {
        return $this->province;
    }

    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @return string
     */
    public function getLastUpdateTime(): ?string
    {
        return $this->lastUpdateTime;
    }

    /**
     * @return int
     */
    public function getLvlScore1(): ?int
    {
        return $this->lvlScore1;
    }

    /**
     * @return int
     */
    public function getLvlScore2(): ?int
    {
        return $this->lvlScore2;
    }

    /**
     * @return int
     */
    public function getLvlScore3(): ?int
    {
        return $this->lvlScore3;
    }

    /**
     * @return int
     */
    public function getLvlScore4(): ?int
    {
        return $this->lvlScore4;
    }

    /**
     * @return int
     */
    public function getLvlScore5(): ?int
    {
        return $this->lvlScore5;
    }

    /**
     * @return int
     */
    public function getLvlScore6(): ?int
    {
        return $this->lvlScore6;
    }

    /**
     * @return int
     */
    public function getGoldScore(): ?int
    {
        return $this->goldScore;
    }

    /**
     * @return int
     */
    public function getTimes(): ?int
    {
        return $this->times;
    }

    /**
     * @return int
     */
    public function getBomb(): ?int
    {
        return $this->bomb;
    }

    /**
     * @return int
     */
    public function getEvenFrying(): ?int
    {
        return $this->evenFrying;
    }

    /**
     * @return int
     */
    public function getSpring(): ?int
    {
        return $this->spring;
    }

    /**
     * @return int
     */
    public function getWinGold(): ?int
    {
        return $this->winGold;
    }

}
