package com.gionee.pay.web.service;

import java.io.IOException;

import net.minidev.json.JSONObject;
import net.minidev.json.JSONStyle;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.junit.runners.JUnit4;
import org.xml.sax.SAXException;

import com.gionee.pay.util.JsonUtils;

/**
 * Tests for {@link pay.gionee.com.web.service.NotifyUpdate}.
 * 
 * @author tianxb
 * @since 0.7.1
 */
@RunWith(JUnit4.class)
public class PayKeyUpdateTest {
	private static final String PAYKEY_UPDATE_URL = "http://test3.gionee.com/pay-merchant/agent/paykey/update";

	@Test
	public void testPayKeyUpdate() throws IOException, SAXException {

		JSONObject json = new JSONObject();
		json.put("agent_id", "8AF43DE9862C46EBB1F91A36299057EE");
		json.put("api_key", "CC6D46E131B74980B8763FA5EBF2E29D");

		String body = json.toJSONString(JSONStyle.NO_COMPRESS);

		// WebRequest request = new PostMethodWebRequest(NOTIFY_UPDATE_URL);
		// request.setParameter("api_key", "process_zip_file");
		// request.setParameter("zip_loc", "c:\\test.zip");
		// WebConversation wc = new WebConversation();
		// HttpUnitOptions.setScriptingEnabled(false);
		// HttpUnitOptions.setExceptionsThrownOnScriptError(false);
		// WebResponse resp = wc.getResponse(request);
		// assertEquals(resp.getText().trim(), "applicationName");

		String response = JsonUtils.post(PAYKEY_UPDATE_URL, body);
		System.out.println("response :" + response);
	}
}
