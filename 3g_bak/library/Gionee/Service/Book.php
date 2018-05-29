<?php

class Gionee_Service_Book {
	
	/**
	 * 
	 * @return Gionee_Dao_Book
	 */
	public static function BookDao(){
		return Common::getDao("Gionee_Dao_Book");
	}
	
	/**
	 * 
	 * @return Gionee_Dao_BookCategory
	 */
	public static function BookCategoryDao(){
		return Common::getDao("Gionee_Dao_BookCategory");
	}
	
	/**
	 * @return Gionee_Dao_BookRank
	 */
	public static function BookRankDao(){
		return Common::getDao("Gionee_Dao_BookRank");
	}
	/**
	 * @return Gionee_Dao_BookContent
	 */
	public static function BookContentDao(){
		return Common::getDao("Gionee_Dao_BookCategory");
	}
	
	public static function BookChapterDao(){
		return Common::getDao("Gionee_Dao_BookChapter");
	}
}