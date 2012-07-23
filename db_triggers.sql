DROP TRIGGER IF EXISTS before_insert_ab_bookings;
DROP TRIGGER IF EXISTS before_update_ab_bookings;
DROP TRIGGER IF EXISTS after_update_global_dates;
DROP TRIGGER IF EXISTS after_update_ab_accounts;

DELIMITER |

# --- mp_content --- #

CREATE TRIGGER `before_insert_ab_bookings` BEFORE INSERT ON `ab_bookings` FOR EACH ROW
BEGIN
	SET NEW.userName =
		( SELECT fullName
			FROM global_users
			WHERE global_users.ID = NEW.userID) ;
	SET NEW.placing =
		( SELECT `placing`
			FROM ab_placing WHERE ab_placing.ID = NEW.placingID) ;
	SET NEW.category =
		( SELECT `category`
			FROM ab_categories WHERE ab_categories.ID = NEW.categoryID) ;
	SET NEW.marketer =
		( SELECT `marketer`
			FROM ab_marketers WHERE ab_marketers.ID = NEW.marketerID) ;
	SET NEW.publishDate =
		( SELECT `publish_date`
			FROM global_dates WHERE global_dates.ID = NEW.dID) ;
	IF NEW.colourID THEN
		SET NEW.colour =
			( SELECT `colour`
				FROM ab_colour_rates WHERE ab_colour_rates.ID = NEW.colourID) ;
		SET NEW.colourLabel =
			( SELECT `label`
				FROM ab_colour_rates WHERE ab_colour_rates.ID = NEW.colourID) ;
	END IF;
	IF NEW.material_productionID THEN
		SET NEW.material_production =
			( SELECT `production`
				FROM ab_production WHERE ab_production.ID = NEW.material_productionID) ;
	END IF;
	IF NEW.accountID THEN
		SET NEW.accNum =
			( SELECT `accNum`
				FROM ab_accounts WHERE ab_accounts.ID = NEW.accountID) ;
	END IF;
	IF NEW.checked_userID THEN
		SET NEW.checked_user =
			( SELECT `fullName`
				FROM global_users WHERE global_users.ID = NEW.checked_userID) ;
	END IF;
	IF NEW.deleted_userID THEN
		SET NEW.deleted_user =
			( SELECT `fullName`
				FROM global_users WHERE global_users.ID = NEW.deleted_userID) ;
	END IF;
	IF NEW.insertTypeID THEN
		SET NEW.insertLabel =
			( SELECT `insertsLabel`
				FROM ab_inserts_types WHERE ab_inserts_types.ID = NEW.insertTypeID) ;
	END IF;
END|
CREATE TRIGGER `before_update_ab_bookings` BEFORE UPDATE ON `ab_bookings` FOR EACH ROW
BEGIN

	IF  (NEW.userID <> OLD.userID) THEN
		SET NEW.userName =
			( SELECT `fullName`
				FROM global_users
				WHERE global_users.ID = NEW.userID) ;
	END IF;
	IF  (NEW.placingID <> OLD.placingID) THEN
		SET NEW.placing =
			( SELECT `placing`
				FROM ab_placing
				WHERE ab_placing.ID = NEW.placingID) ;
	END IF;
	IF  (NEW.categoryID <> OLD.categoryID) THEN
		SET NEW.category =
			( SELECT `category`
				FROM ab_categories
				WHERE ab_categories.ID = NEW.categoryID) ;
	END IF;
	IF  (NEW.marketerID <> OLD.marketerID) THEN
		SET NEW.marketer =
			( SELECT `marketer`
				FROM ab_marketers
				WHERE ab_marketers.ID = NEW.marketerID) ;
	END IF;
	IF (NEW.dID <> OLD.dID) THEN
		SET NEW.publishDate =
			( SELECT `publish_date`
				FROM global_dates
				WHERE global_dates.ID = NEW.dID) ;
	END IF;
	IF (NEW.accountID <> OLD.accountID) THEN
		SET NEW.accNum =
			( SELECT `accNum`
				FROM ab_accounts
				WHERE ab_accounts.ID = NEW.accountID) ;
	END IF;


	IF NEW.material_productionID THEN
		SET NEW.material_production =
			( SELECT `production`
				FROM ab_production
				WHERE ab_production.ID = NEW.material_productionID) ;

	ELSE
		IF (OLD.material_productionID)
			THEN
			SET NEW.material_production =
				( SELECT `production`
					FROM ab_production
					WHERE ab_production.ID = OLD.material_productionID) ;
		END IF;
	END IF;


	IF  (NEW.colourID <> OLD.colourID)	THEN
		SET NEW.colour =
			( SELECT `colour`
				FROM ab_colour_rates
				WHERE ab_colour_rates.ID = NEW.colourID) ;
		SET NEW.colourLabel =
			( SELECT `label`
				FROM ab_colour_rates
				WHERE ab_colour_rates.ID = NEW.colourID) ;

	END IF;

	IF (NEW.checked_userID <> OLD.checked_userID) THEN
		SET NEW.checked_user =
			( SELECT `fullName`
				FROM global_users
				WHERE global_users.ID = NEW.checked_userID) ;
	END IF;
	IF (NEW.deleted_userID <> OLD.deleted_userID) THEN
		SET NEW.deleted_user =
			( SELECT `fullName`
				FROM global_users
				WHERE global_users.ID = NEW.deleted_userID) ;
	END IF;
	IF
		(NEW.insertTypeID <> OLD.insertTypeID)
		THEN
		SET NEW.insertLabel =
			( SELECT `insertsLabel`
				FROM ab_inserts_types
				WHERE ab_inserts_types.ID = NEW.insertTypeID) ;
	END IF;

END|



CREATE TRIGGER `after_update_global_dates` AFTER UPDATE ON `global_dates` FOR EACH ROW
BEGIN

	IF NEW.publish_date <> OLD.publish_date	THEN
		UPDATE ab_bookings SET publishDate = NEW.publish_date WHERE ab_bookings.dID = NEW.ID ;
	END IF;

END|


CREATE TRIGGER `after_update_ab_accounts` AFTER UPDATE ON `ab_accounts` FOR EACH ROW
BEGIN

	IF NEW.accNum <> OLD.accNum	THEN
		UPDATE ab_bookings SET accNum = NEW.accNum WHERE ab_bookings.accountID = NEW.ID ;
	END IF;

END|




