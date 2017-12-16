create table kiosk_config_change_log select * from kiosk_config_item where 1=0;
DELIMITER $$
Create trigger kiosk_config_change_log_trigger after update on kiosk_config_item
for each row
begin
IF NEW.status = 'Inactive' THEN
insert into kiosk_config_change_log
(select aa.* from kiosk_config_item as aa where aa.id = new.id);
        
END IF;
end;
 $$
DELIMITER ;
