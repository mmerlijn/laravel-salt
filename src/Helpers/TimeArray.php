<?php
namespace mmerlijn\LaravelSalt\Helpers;

class TimeArray
{
    private array $timeArray = [];

    public function __construct(?array $timeArray = null)
    {
        if ($timeArray) {
            $this->create($timeArray);
        }
    }

    /** Initialising timeArray
     *
     * @param array $array
     * @return TimeArray
     */
    public function create(array $array): TimeArray
    {
        if (!isset($array[0][0])) {
            if (count($array)) {
                $array = [$array];
            }
        }
        $this->timeArray = $array;
        $this->compact();
        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->timeArray;
    }

    /** adds times to array
     * @param array $times
     * @return TimeArray
     */
    public function add(array $times): TimeArray
    {
        if (isset($times[0])) {
            if (is_array($times[0])) {
                foreach ($times as $time) {
                    $this->timeArray[] = $time;
                }
            } else {
                $this->timeArray[] = $times;
            }
            $this->compact();
        }
        return $this;
    }

    /** Given a duration, splits the timeArray in chunks of that duration
     *
     * @param int $duration
     * @return array
     */
    public function split(int $duration): array
    {
        $splitTimes = [];
        foreach ($this->timeArray as $times) {
            for ($i = $times[0] + $duration; $i <= $times[1]; $i += $duration) {
                $splitTimes[] = [$i - $duration, $i];
            }
        }
        return $splitTimes;
    }


    /** substract times from the TimeArray
     * @param array $times
     * @return TimeArray
     */
    public function subtract(array $times): TimeArray
    {
        if (isset($times[0])) {
            if (is_array($times[0])) {
                foreach ($times as $time) {
                    $this->subtract($time);
                }
            } else {
                list($s_, $e_) = $times;
                $length = count($this->timeArray);
                for ($i = 0; $i < $length; $i++) {
                    list($s, $e) = $this->timeArray[$i];
                    //voorliggend
                    //blok     --------
                    //sit 1  -----
                    //sit 2    -----
                    //sit 3  ------------
                    if ($s_ <= $s and $e_ > $s) {
                        if ($e_ < $e) {
                            $this->timeArray[$i] = [$e_, $e];
                        } else {
                            unset($this->timeArray[$i]);
                        }
                        //overliggend
                        //blok          ---------
                        //sit 1            ---
                        //sit 2            --------
                    } elseif ($s_ > $s and $s_ < $e) {
                        $this->timeArray[] = [$s, $s_];
                        if ($e_ < $e) {
                            $this->timeArray[$i] = [$e_, $e];
                        } else {
                            unset($this->timeArray[$i]);
                        }
                    }
                }
            }
            sort($this->timeArray);
        }
        return $this;
    }


    /**joins the timeArray if end time and start time are equal or overlap
     * @return void
     */

    private function compact(): void
    {
        sort($this->timeArray);
        $length = count($this->timeArray);
        foreach ($this->timeArray as $k=>$ta){
            if($ta[0]>=$ta[1]){
                unset($this->timeArray[$k]);
                break;
            }
            if(array_key_last($this->timeArray)==$k){
                break;
            }
            list($s_, $e_) = $ta;
            list($s, $e) = $this->timeArray[$k+1];
            if ($e_ >= $s) { //same start time
                $this->timeArray[$k] = [$s_, max($e_, $e)];
                unset($this->timeArray[$k+1]);
                break;
            }
        }
        if ($length != count($this->timeArray)) {
            $this->compact();
        }
    }

    public function __toString()
    {
        $tmp= [];
        foreach ($this->timeArray as $a){
            $tmp[] = "[".implode(",", $a)."]";
        }
        return "[".implode(",", $tmp)."]";
    }

    public function intersect(array $times): TimeArray
    {
        $intersect = [];
        foreach ($this->timeArray as $time) {
            foreach ($times as $time2) {
                $intersect[] = [max($time[0], $time2[0]), min($time[1], $time2[1])];
            }
        }
        $this->timeArray = $intersect;
        $this->compact();
        return $this;
    }
    public function forHumans(): string
    {
        $result = [];
        foreach ($this->timeArray as $time) {
            $result[] = $this->int2Time($time[0]) . "-" . $this->int2Time($time[1]);
        }
        return implode(", ", $result);
    }
    private function int2Time(int $time): string
    {
        $hours = floor($time / 60);
        $minutes = $time % 60;
        return $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
    }


}
