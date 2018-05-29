package com.gionee.pay.web.service;

import java.io.IOException;
import java.util.Date;

import net.minidev.json.JSONObject;
import net.minidev.json.JSONStyle;

import org.junit.Test;

import pay.gionee.com.util.SubmitTimeUtil;

import com.gionee.pay.util.JsonUtils;

public class AppApplyTest {
	private static final String APP_APPLY_URL = "http://test3.gionee.com/pay-merchant/agent/app/apply";

	@Test
	public void testAppApply() throws IOException {
		JSONObject json = new JSONObject();
		json.put("agent_id", "8AF43DE9862C46EBB1F91A36299057EE");
		json.put("submit_time", SubmitTimeUtil.toString(new Date()));
		json.put("package_name", "金立游戏开发者中心测试环境应用test6");

		json.put("app_name", "金立游戏开发者中心测试环境应用test6");
		json.put("type", "单机");
		json.put("channel", "游戏大厅");

		String body = json.toJSONString(JSONStyle.NO_COMPRESS);

		// WebRequest request = new PostMethodWebRequest(NOTIFY_UPDATE_URL);
		// request.setParameter("api_key", "process_zip_file");
		// request.setParameter("zip_loc", "c:\\test.zip");
		// WebConversation wc = new WebConversation();
		// HttpUnitOptions.setScriptingEnabled(false);
		// HttpUnitOptions.setExceptionsThrownOnScriptError(false);
		// WebResponse resp = wc.getResponse(request);
		// assertEquals(resp.getText().trim(), "applicationName");

		String response = JsonUtils.post(APP_APPLY_URL, body);
		System.out.println("response :" + response);
	}
}
