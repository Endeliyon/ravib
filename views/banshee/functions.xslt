<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!--
//
//  Internal error template
//
//-->
<xsl:template match="internal_errors">
<div id="internal_errors" class="panel panel-danger">
<div class="panel-heading">
	<h3 class="panel-title">Internal errors <span title="Close message box" alt="Close" class="close" onClick="javascript:document.getElementById('internal_errors').style.display = 'none'">x</span></h3>
</div>
<div class="panel-body">
	<xsl:value-of disable-output-escaping="yes" select="/output/internal_errors" />
</div>
</div>
</xsl:template>

<!--
//
//  Result template
//
//-->
<xsl:template match="result">
<p><xsl:value-of select="." /></p>
<xsl:choose>
	<xsl:when test="@url and @seconds">
		<xsl:call-template name="redirect">
			<xsl:with-param name="url" select="@url" />
			<xsl:with-param name="seconds" select="@seconds" />
		</xsl:call-template>
	</xsl:when>
	<xsl:when test="@url">
		<xsl:call-template name="redirect"><xsl:with-param name="url" select="@url" /></xsl:call-template>
	</xsl:when>
	<xsl:when test="@seconds">
		<xsl:call-template name="redirect"><xsl:with-param name="seconds" select="@seconds" /></xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<xsl:call-template name="redirect" />
	</xsl:otherwise>
</xsl:choose>
</xsl:template>

<!--
//
//  Redirect page template
//
//-->
<xsl:template name="redirect">
<xsl:param name="url" select="/output/page" />
<xsl:param name="seconds">3</xsl:param>
<xsl:if test="$seconds>0">
<p>Klik <a href="/{$url}">hier</a> om door te gaan of wacht <xsl:value-of select="$seconds" /> seconden om doorgestuurd te worden.</p>
<script type="text/javascript">
&lt;!--
	function redirect() {
		document.location = "/<xsl:value-of select="$url" />";
	}

	setTimeout("redirect()", <xsl:value-of select="$seconds" />000);
//-->
</script>
</xsl:if>
</xsl:template>

<!--
//
//  Show system messages
//
//-->
<xsl:template match="system_messages">
<div class="alert alert-info" role="alert">
<xsl:for-each select="message">
    <p>&#187; <xsl:value-of select="." /></p>
</xsl:for-each>
</div>
</xsl:template>

<!--
//
//  Show system warnings
//
//-->
<xsl:template match="system_warnings">
<div class="alert alert-danger" role="alert">
<xsl:for-each select="warning">
    <p>&#187; <xsl:value-of select="." /></p>
</xsl:for-each>
</div>
</xsl:template>

<!--
//
//  Show messages template
//
//-->
<xsl:template name="show_messages">
<xsl:if test="/output/messages/message">
<div class="alert alert-warning">
<xsl:for-each select="/output/messages/message">
	<div><xsl:value-of select="." /></div>
</xsl:for-each>
</div>
</xsl:if>
</xsl:template>

<!--
//
//  Website error template
//
//-->
<xsl:template match="website_error">
<xsl:choose>
	<xsl:when test=".=200">
		Although no error occurred, you ended up at the error page.
	</xsl:when>
	<xsl:when test=".=401">
		You are not authorized to view this page.
	</xsl:when>
	<xsl:when test=".=403">
		You are not allowed to view this page.
	</xsl:when>
	<xsl:when test=".=404">
		The requested page could not be found.
	</xsl:when>
	<xsl:otherwise>
		An internal error has occurred.
	</xsl:otherwise>
</xsl:choose>
</xsl:template>

<!--
//
//  Show breadcrumbs
//
//-->
<xsl:template match="breadcrumbs">
<ul class="breadcrumbs">
<xsl:for-each select="crumb">
<li>
<xsl:if test="@current='yes'"><xsl:attribute name="class">current</xsl:attribute></xsl:if>
<a href="/{@link}"><xsl:value-of select="." /></a>
</li>
</xsl:for-each>
</ul>
</xsl:template>

</xsl:stylesheet>
