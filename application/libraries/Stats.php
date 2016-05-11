<?php

/**
 * Stats library.
 * Provides stats about toolwatch measures.
 * Heavy use of caching ahead.
 */
class Stats {

  /**
   * Constructor.
   * Warm up cache if needs be.
   */
  public function __construct() {

    $this->CI =& get_instance();
    $this->CI->load->driver('cache', array('adapter' => 'apc'));

    //If @tw doesn't exists, it means the cache is cold
    if(!$this->exists("@tw")){

      log_message('info', "Populating cache for stats");

      $this->init("@tw");
      $this->populateCache(0, -300, '_neg_');
      $this->populateCache(300, 0, '_pos_');
    }
  }

  /**
   * Create an empty cache entry for $brand
   * @param  String $brand
   */
  private function init($brand){

    log_message('info', "init cache for " . $brand);

    $this->CI->cache->save($brand.'_neg_'.'count', 0.0);
    $this->CI->cache->save($brand.'_neg_'.'sum', 0.0);
    $this->CI->cache->save($brand.'_pos_'.'sum', 0.0);      $this->CI->cache->save($brand.'_pos_'.'count', 0.0);
  }

  /**
   * Checks if $brand has a cache entry
   * @param  String $brand
   */
  private function exists($brand){
    if($this->CI->cache->get($brand.'_neg_sum') === false
    || $this->CI->cache->get($brand.'_neg_count') === false
    || $this->CI->cache->get($brand.'_pos_sum') === false
    || $this->CI->cache->get($brand.'_pos_count') === false){
      return false;
    }
    return true;
  }

  /**
   * Increment $key by $value
   *
   * I had to implement this instead of using
   * $this->CI->cache->inc that only works on integer
   *
   * @param  String $key
   * @param  float  $value
   */
  private function floatingPointInc($key, $value = 1.0){

    $this->CI->cache->save(
      $key,
      (float)$this->CI->cache->get($key)+(float)$value
    );
  }

  /**
   * Populate cache according to $accuracyUpperBound $accuracyLowerBound and a $preValue
   * @param  Integer $accuracyUpperBound
   * @param  Integer $accuracyLowerBound
   * @param  String $preValue
   */
  private function populateCache($accuracyUpperBound, $accuracyLowerBound, $preValue){

    //Load the measure_precision view
    $this->measure = new MY_Model('measure_precision');

    //Group by brand
    $measures = $this->measure
      ->select('SUM(accuracy) as accuracy, count(1) as count, brand')
      ->where('accuracy >', $accuracyLowerBound)
      ->where('accuracy <', $accuracyUpperBound)
      ->group_by('brand')
      ->find_all();

    foreach ($measures as $measure) {

      //Create the cache entry if needed
      if(!$this->exists($measure->brand)){
        $this->init($measure->brand);
      }

      // Update the cache entries
      $this->floatingPointInc($measure->brand . $preValue . 'sum', $measure->accuracy);
      $this->floatingPointInc($measure->brand . $preValue . 'count', $measure->count);

      $this->floatingPointInc("@tw" . $preValue . 'sum', $measure->accuracy);
      $this->floatingPointInc("@tw" . $preValue . 'count', $measure->count);
    }
  }

  /**
   * Get the negative precision average for $brand
   * @param  String $brand
   * @return Float
   */
  public function getNegAverage($brand){

    if(($count = $this->CI->cache->get($brand . '_neg_count'))
    && ($sum = $this->CI->cache->get($brand . '_neg_sum'))){
      log_message('info', 'neg cache hit for ' . $brand . ' [count ='.$count.', sum='.$sum . ']');
      return $sum / $count;
    }
    return 0.0;
  }

  /**
   * Get the positive precision average for $brand
   * @param  String $brand
   * @return Float
   */
  public function getPosAverage($brand){

    if(($count = $this->CI->cache->get($brand . '_pos_count'))
    && ($sum = $this->CI->cache->get($brand . '_pos_sum'))){
      log_message('info', 'pos cache hit for ' . $brand. ' [count ='.$count.', sum='.$sum . ']');
      return $sum / $count;
    }
    return 0.0;
  }

  /**
   * Update the $brand cache entry with $accuracy
   * @param  String $brand
   * @param  Float $accuracy
   */
  public function update($brand, $accuracy){

    $pre = ($accuracy < 0) ? '_neg_' : '_pos_';

    if(!$this->exists($brand)){ $this->init($brand);}

    $this->floatingPointInc($brand . $pre . 'count');
    $this->floatingPointInc($brand . $pre . 'sum', $accuracy);
    log_message('info', 'update cache for ' . $brand);

    $this->floatingPointInc("@tw" . $pre . 'count');
    $this->floatingPointInc("@tw" . $pre . 'sum', $accuracy);

  }

}

?>
