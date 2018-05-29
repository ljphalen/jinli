package com.gionee.game.search.server.bean.resultBeans;

import java.util.List;

import com.gionee.game.search.server.bean.resultBean.PinyinBean;

public class PinyinBeans extends BaseBeans {
	private static final long serialVersionUID = -2988057672601135123L;
	
	// 候选拼音纠错词，调试用 
	private List<PinyinBean> candidatePinyinBeans;

	public List<PinyinBean> getCandidatePinyinBeans() {
		return candidatePinyinBeans;
	}

	public void setCandidatePinyinBeans(List<PinyinBean> candidatePinyinBeans) {
		this.candidatePinyinBeans = candidatePinyinBeans;
	}
}
