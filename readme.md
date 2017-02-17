mysqlbinlog事件采集系统
====
>不改变一句业务代码实现整库数据变化实时监控

###事件数据
* 1、发生数据变化的数据库名称
* 2、发生数据变化的数据表名称
* 3、实际变化的数据

   
    `array(3) {
      ["event_type"]=> 
      string(11) "update_rows"
      ["time"]=> 
      string(19) "2017-02-13 17:02:56"
      ["data"]=>
      array(2) {
        ["old_data"]=>
        array(5) {
          ["id"]=>
          string(3) "528"
          ["day_payout_money"]=>
          string(8) "2000.000"
          ["day"]=>
          string(1) "1"
          ["created_at"]=>
          string(10) "1486547863"
          ["updated_at"]=>
          string(10) "1486622467"
        }
        ["new_data"]=>
        array(5) {
          ["id"]=>
          string(3) "528"
          ["day_payout_money"]=>
          string(8) "2000.000"
          ["day"]=>
          string(1) "2"
          ["created_at"]=>
          string(10) "1486547863"
          ["updated_at"]=>
          string(10) "1486622467"
        }
      }
    }`

>event_type 为事件类型，三者之一 update_rows、delete_rows、write_rows
 如果是update_rows，则data部分包含new_data和old_data两部分，分别代表修改后和修改前的数据
 如果是delete_rows或者write_rows，data部分则仅包含变化的数据，time为事件发生的具体时间
 
 
###启动服务
    php app server:start
    //可选项 --d以守护进程启动


