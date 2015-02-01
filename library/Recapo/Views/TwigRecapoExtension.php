<?php
namespace Recapo\Views;

use Slim\Slim;

class TwigRecapoExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'recapo';
    }

    public function getFunctions()
    {
      return array(
          new \Twig_SimpleFunction('date_difference', function ($start, $end, $type = 'us') {
              $startDatetime = date_create($start);
              $endDatetime = date_create($end);
              $differenceSeconds = (date_timestamp_get($endDatetime) - date_timestamp_get($startDatetime));

              if($type == 'us') {
                $differenceSeconds *= 1000000;
                $startDatetimeU = date_format($startDatetime, 'u');
                $endDatetimeU = date_format($endDatetime, 'u');
                

                if($endDatetimeU < $startDatetimeU) {
                  $endDatetimeU += 1000000;
                  $differenceSeconds -= 1000000;
                }
                $differenceMicroseconds = ($endDatetimeU - $startDatetimeU);
                
                return $differenceSeconds + $differenceMicroseconds;
              } else {
                return $differenceSeconds;
              }
            }
          )
        );
    }
    
    public function getFilters()
    {
      return array(
          new \Twig_Filter_Function('var_dump')
        );
    }
}
