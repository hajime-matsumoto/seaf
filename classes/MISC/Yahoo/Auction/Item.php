<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\MISC\Yahoo\Auction;

use Seaf\Container\ArrayContainer;
use Seaf;

/**
 * オークションアイテム
 */
class Item
{
    /**
     * @var array
     */
    public $result;

    /**
     * コンストラクタ
     *
     * @param array
     */
    public function __construct ($result)
    {
        $this->result = new ArrayContainer($result);
        //var_dump($this->result);
    }

    /**
     * 現在の価格
     */
    public function getCurrentPrice ( )
    {
        return ceil($this->result->get('Price','-'));
    }

    /**
     * 参考価格
     */
    public function getExamplePrice ( )
    {
        return $this->getCurrentPrice( );
    }

    /**
     * 開始日時
     */
    public function getStartTime ( )
    {
        return date(
            'Y-m-d G:i',
            strtotime($this->result->get('StartTime','-'))
        );
    }

    /**
     * 終了日時
     */
    public function getEndTime ( )
    {
        return date(
            'Y-m-d G:i',
            strtotime($this->result->get('EndTime','-'))
        );
    }

    /**
     * オークションが終了していれば真
     *
     * @return true
     */
    public function isEnded( )
    {
        $sec = strtotime($this->result->get('EndTime')) - time();
        return $sec < 0 ? true: false;
    }

    /**
     * 残り時間
     */
    public function getTimeLeft ( )
    {
        $days  = 0;
        $hours = 0;
        $mins  = 0;


        $one_mins = 60; // 1分
        $one_hour = $one_mins * 60; // 1時間
        $one_day  = $one_hour * 24; // 1日

        $sec = strtotime($this->result->get('EndTime')) - time();

        if ($sec < 0) {
            return 0;
        }

        if ($one_day < $sec) {
            $days = floor($sec / $one_day);
            $sec = $sec % $one_day;
        }
        if ($one_hour < $sec) {
            $hours = floor($sec / $one_hour);
            $sec = $sec % $one_hour;
        }
        if ($one_mins < $sec) {
            $mins = floor($sec / $one_mins);
            $sec = $sec % $one_mins;
        }
        return Seaf::P18n( )
            ->getTranslation('MESSAGE.AUCTION')
            ->get('TIME_LEFT', $days, $hours, $mins, $sec);
    }

    /**
     * 入札件数
     */
    public function getBids ( )
    {
        return $this->result->get('Bids');
    }

    /**
     * 商品数量
     */
    public function getQuantity ( )
    {
        return $this->result->get('Quantity');
    }

    /**
     * 開始価格
     */
    public function getInitPrice ( )
    {
        return ceil($this->result->get('Initprice'));
    }

    /**
     * 入札単位
     */
    public function getBidUnit ( )
    {
        $price = $this->getInitPrice( );

        if( $price >= 50000 ){
            return 1000;
        }elseif( $price >= 10000 ){
            return 500;
        }elseif( $price >= 5000 ){
            return 250;
        }elseif( $price >= 1000 ){
            return 100;
        }else{
            return 10;
        }
    }

    /**
     * 即決価格
     */
    public function getBidorbuy ( )
    {
        return ceil($this->result->get('Bidorbuy'));
    }

    /**
     * 即決価格があるか
     */
    public function hasBidorbuy ( )
    {
        $price = $this->getBidorbuy( );
        if ($price > 0) {
            return 1;
        }
        return 0;
    }


    /**
     * アイテムステータス/コンディション
     *
     * @todo 翻訳
     */
    public function getItemStatusCondition ( )
    {
        return $this->result->get('ItemStatus.Condition');
    }

    /**
     * 出品タイプ
     */
    public function getItemType ( )
    {
        if ($this->result->get('Option.StoreIcon')) {
            return 'store';
        }else{
            return 'indivisual';
        }
    }

    /**
     * 送料無料
     */
    public function isFreeShipping ( )
    {
        if ($this->result->get('Option.FreeshippingIcon')) {
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 送料負担者
     */
    public function getChargeForShipping ( )
    {
        return $this->result->get('ChargeForShipping');
    }

    /**
     * オークションIDを取得
     */
    public function getAuctionID ( )
    {
        return $this->result->get('AuctionID');
    }

    /**
     * 商品名を取得
     */
    public function getTitle( )
    {
        return $this->result->get('Title');
    }

    /**
     * 商品説明を取得
     */
    public function getDescription( )
    {
        return $this->result->get('Description');
    }

    /**
     * 写真リストを取得
     */
    public function getImgList( )
    {
        $list = [];
        foreach ($this->result->get('Img', []) as $k=>$v)
        {
            $name = substr($k, 0, strlen('Image')+1);
            $key = substr($k, strlen('Image')+1);
            if (empty($key)) $key = 'url';
            $list[$name][strtolower($key)] = $v;
        }
        return $list;
    }

    /**
     * 出品者ID
     */
    public function getSellerId( )
    {
        return $this->result->get('Seller.Id');
    }

    /**
     * 出品者レーティング Good
     */
    public function getSellerTotalGoodRating( )
    {
        return $this->result->get('Seller.Rating.TotalGoodRating');
    }

    /**
     * 出品者評価 Bad
     */
    public function getSellerTotalBadRating( )
    {
        return $this->result->get('Seller.Rating.TotalBadRating');
    }

    /**
     * 商品発送元
     */
    public function getLocation( )
    {
        return $this->result->get('Location');
    }

    /**
     * 最低入札額
     */
    public function getBidMinPrice( )
    {
        return $this->getCurrentPrice() + $this->getBidUnit();
    }

    /**
     * 最高入札者を取得する
     */
    public function getHighestBidder( )
    {
        $bidders = [];
        $c = $this->result->get('HighestBidders.totalHighestBidders');

        if ($c == 0 || empty($c)) return $bidders;

        if ($c == 1) {
            $bidders[] = [
                'id'    => $this->result->get('HighestBidders.Bidder.Id'),
                'point' => $this->result->get('HighestBidders.Bidder.Rating.Point')
            ];
        } else {
            foreach ($this->result->get('HighestBidders.Bidder', array()) as $v) {
                $bidders[] = [
                    'id' => $v['Id'],
                    'point' => $v['Rating']['Point']
                ];
            }
        }
        return $bidders;
    }

    /**
     * 最高入札者表示のテキストを作成する
     */
    public function getHighestBidderText( )
    {
        if (empty($this->getHighestBidder())) {
            return '-';
        }
        $str = [];
        foreach ($this->getHighestBidder() as $v) {
            $str[] = $v['id'].'/ 評価:'.$v['point'];
        }
        return implode(', ', $str);
    }

    /**
     * オークションページ
     */
    public function getAuctionItemUrl ( )
    {
        return $this->result->get('AuctionItemUrl');
    }
}
