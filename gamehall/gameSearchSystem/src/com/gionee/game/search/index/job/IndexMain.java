package com.gionee.game.search.index.job;

import org.springframework.context.support.ClassPathXmlApplicationContext;

public class IndexMain {
	public static void main(String[] args) {
		new ClassPathXmlApplicationContext(
				new String[] { "applicationContext-dao.xml",
						"applicationContext-job.xml" });
	}
}
