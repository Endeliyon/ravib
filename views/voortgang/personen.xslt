<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<form action="/{/output/page}/{people/@case_id}" method="post" class="search">
<input type="text" id="search" name="search" placeholder="Search" class="form-control" />
<input type="hidden" name="submit_button" value="search" />
</form>

<table class="table table-condensed table-striped table-hover">
<thead>
<tr>
<th>Naam</th>
<th>E-mailadres</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="people/person">
<tr class="click" onClick="javascript:document.location='/{/output/page}/{../@case_id}/{@id}'">
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="email" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group left">
<a href="/{/output/page}/{people/@case_id}/new" class="btn btn-default">Nieuw persoon</a>
<a href="/voortgang/{people/@case_id}" class="btn btn-default">Terug</a>
</div>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}/{@case_id}" method="post">
<xsl:if test="person/@id">
<input type="hidden" name="id" value="{person/@id}" />
</xsl:if>

<label for="email">Naam:</label>
<input type="text" id="name" name="name" value="{person/name}" class="form-control" />
<label for="email">E-mailadres:</label>
<input type="text" id="email" name="email" value="{person/email}" class="form-control" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Persoon opslaan" class="btn btn-default" />
<a href="/{/output/page}/{@case_id}" class="btn btn-default">Afbreken</a>
<xsl:if test="person/@id">
<input type="submit" name="submit_button" value="Persoon verwijderen" class="btn btn-default" onClick="javascript:return confirm('VERWIJDEREN: Weet je het zeker?')" />
</xsl:if>
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Personen</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
