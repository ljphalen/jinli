package com.gionee.game.search.test;

import java.util.ArrayList;
import java.util.List;

import org.apache.lucene.index.Term;
import org.apache.lucene.queries.BooleanFilter;
import org.apache.lucene.queries.TermsFilter;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.FilteredQuery;
import org.apache.lucene.search.TermQuery;


public class Main {
	public static void main(String[] args) {
		List<Term> terms = new ArrayList<Term>();
		terms.add(new Term("aaa", "444"));
		terms.add(new Term("aaa", "222"));
		terms.add(new Term("aaa", "111"));
		terms.add(new Term("ccc", "333"));
		TermsFilter tsf = new TermsFilter(terms);
		
		BooleanFilter bf = new BooleanFilter();
		bf.add(tsf, BooleanClause.Occur.SHOULD);
		
		bf.add(new TermsFilter(new Term[] {new Term("yyy", "999"),new Term("yyy", "888"),new Term("yyy", "555")}), BooleanClause.Occur.SHOULD);
		bf.add(new TermsFilter(new Term[] {new Term("fff", "999"),new Term("fff", "888"),new Term("fff", "555")}), BooleanClause.Occur.MUST);
		bf.add(new TermsFilter(new Term[] {new Term("xxx", "999"),new Term("xxx", "888"),new Term("xxx", "555")}), BooleanClause.Occur.SHOULD);
		
		BooleanQuery bl = new BooleanQuery();
		bl.add(new TermQuery(new Term("eee", "233423")), BooleanClause.Occur.MUST);
		bl.add(new TermQuery(new Term("ggg", "233423")), BooleanClause.Occur.SHOULD);
		bl.add(new TermQuery(new Term("kkk", "233423")), BooleanClause.Occur.MUST);
		
		FilteredQuery fq = new FilteredQuery(bl, bf);
		System.out.println(fq);
		
//		System.out.println(bf);
//		System.out.println(bl);
	}
}
