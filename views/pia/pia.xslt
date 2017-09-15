<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />

<!--
//
//  Rule template
//
//-->
<xsl:template match="rule">
<xsl:call-template name="show_messages" />
<div class="progress">
	<div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="{percentage}" aria-valuemin="0" aria-valuemax="100" style="width:{percentage}%"><xsl:value-of select="percentage" />%</div>
</div>

<h2><xsl:value-of select="number" /> - <xsl:value-of select="title" /></h2>
<form action="/{/output/page}/{@case_id}" method="post">
<div class="question"><xsl:value-of disable-output-escaping="yes" select="question" /></div>
<div class="options">
	<div class="answer"><input type="radio" name="answer" value="yes" onClick="javascript:show_answer('yes');"><xsl:if test="answer='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input> Ja.</div>
	<div class="answer"><input type="radio" name="answer" value="no" onClick="javascript:show_answer('no');"><xsl:if test="answer='no'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input> Nee.</div>
</div>
<label for="comment">Opmerking:</label>
<textarea name="comment" id="comment" class="form-control"><xsl:value-of select="comment" /></textarea>

<div class="btn-group">
<input type="submit" name="submit_button" value="Naar begin" class="btn btn-default"><xsl:if test="number='1.1'"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if></input>
<input type="submit" name="submit_button" value="Terug" class="btn btn-default"><xsl:if test="number='1.1'"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if></input>
<input type="submit" name="submit_button" value="Verder" class="btn btn-default" id="next_button"><xsl:if test="not(answer)"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if></input>
</div>

<div class="information"><xsl:value-of disable-output-escaping="yes" select="information" /></div>
</form>

<div class="answers">
<div id="yes"><xsl:value-of disable-output-escaping="yes" select="yes" /></div>
<div id="no"><xsl:value-of disable-output-escaping="yes" select="no" /></div>
</div>

<div style="clear:both"></div>
</xsl:template>

<!--
//
//  Read template
//
//-->
<xsl:template match="ready">
<div class="progress">
	<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">100%</div>
</div>

<form action="/{/output/page}/{@case_id}" method="post">
<p>U bent klaar met deze PIA. Ga door naar de rapportage.</p>
<div class="btn-group">
<input type="submit" name="submit_button" value="Naar begin" class="btn btn-default" />
<input type="submit" name="submit_button" value="Terug" class="btn btn-default" />
<a href="/pia/rapport/{@case_id}" class="btn btn-default">Rapportage</a>
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<xsl:apply-templates select="breadcrumbs" />
<h1>Privacy Impact Assessment</h1>
<div class="case"><xsl:value-of select="case" /></div>
<xsl:apply-templates select="rule" />
<xsl:apply-templates select="ready" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
