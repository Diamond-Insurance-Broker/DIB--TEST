DROP PROCEDURE `make_intervals`; DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `make_intervals`(IN `startdate` DATE, IN `enddate` DATE, IN `intval` INT, IN `unitval` VARCHAR(10)) NOT DETERMINISTIC CONTAINS SQL SQL SECURITY DEFINER BEGIN
-- *************************************************************************
-- Procedure: make_intervals()
--    Author: Jinesh
--      Date: 09/08/2021
--
-- Description:
-- This procedure creates a temporary table named time_intervals with the
-- interval_start and interval_end fields specifed from the startdate and
-- enddate arguments, at intervals of intval (unitval) size.
-- *************************************************************************
   declare thisDate DATE;
   declare nextDate DATE;
   set thisDate = startdate;

   -- *************************************************************************
   -- Drop / create the temp table
   -- *************************************************************************
   drop temporary table if exists exceptional_dates;
   create temporary table if not exists exceptional_dates
      (
      entry_date DATE
      
      );

   -- *************************************************************************
   -- Loop through the startdate adding each intval interval until enddate
   -- *************************************************************************
   repeat
      select
         case unitval
            when 'MICROSECOND' then timestampadd(MICROSECOND, intval, thisDate)
            when 'SECOND'      then timestampadd(SECOND, intval, thisDate)
            when 'MINUTE'      then timestampadd(MINUTE, intval, thisDate)
            when 'HOUR'        then timestampadd(HOUR, intval, thisDate)
            when 'DAY'         then timestampadd(DAY, intval, thisDate)
            when 'WEEK'        then timestampadd(WEEK, intval, thisDate)
            when 'MONTH'       then timestampadd(MONTH, intval, thisDate)
            when 'QUARTER'     then timestampadd(QUARTER, intval, thisDate)
            when 'YEAR'        then timestampadd(YEAR, intval, thisDate)
         end into nextDate;

      insert into exceptional_dates select thisDate;
      set thisDate = nextDate;
   until thisDate > enddate
   end repeat;

 END //
 DELIMITER ;