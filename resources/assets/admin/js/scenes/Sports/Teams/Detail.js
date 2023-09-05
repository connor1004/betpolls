/* eslint-disable no-shadow */
import React, { Component } from 'react';
import {
  Page, ChoiceList, Card, Layout
} from '@shopify/polaris';
import SortableTree, * as TreeDataUtil from 'react-sortable-tree';

import Api from '../../../apis/app';
import OptionsHelper from '../../../helpers/OptionsHelper';

class Detail extends Component {
  constructor(props) {
    super(props);
    const selected_bet_types = (props.location.state.data && props.location.state.data.bet_types)
      ? props.location.state.data.bet_types.map(value => `${value.id}`) : [];

    this.state = {
      bet_types: [],
      selected_bet_types,
      league: props.location.state.data,
      league_divisions: []
    };

    this.handleGoBack = this.handleGoBack.bind(this);
    this.handleBetTypesChange = this.handleBetTypesChange.bind(this);
  }

  async componentDidMount() {
    let {
      league
    } = this.state;

    const {
      params
    } = this.props.match;

    if (!league) {
      const { body } = await Api.get(`admin/sports/league/${params.id}`);
      league = body;
      const selected_bet_types = (league && league.bet_types) ? league.bet_types.map(value => `${value.id}`) : [];
      await this.setState({
        league,
        selected_bet_types
      });
    }

    const { body } = await Api.get('admin/sports/bet-types');
    const bet_types = OptionsHelper.getOptions(body, 'id', 'name');
    let { body: league_divisions } = await Api.get(`admin/sports/league-divisions?league_id=${params.id}`);
    league_divisions = TreeDataUtil.getTreeFromFlatData({
      flatData: league_divisions,
      getKey: node => node.id,
      getParentKey: node => node.parent_id,
      rootKey: 0
    });
    await this.setState({
      bet_types,
      league_divisions
    });
  }

  handleGoBack() {
    this.props.history.goBack();
  }

  async handleBetTypesChange(selected_bet_types) {
    const {
      league
    } = this.state;
    this.setState({
      selected_bet_types
    });

    const params = {
      bet_type_ids: selected_bet_types
    };

    const { body, response } = await Api.post(`admin/sports/leagues/${league.id}/attach-bet-types`, params);
    switch (response.status) {
      case 200:
      default: {
        const new_selected_bet_types = body.map(value => `${value.id}`);
        this.setState({
          selected_bet_types: new_selected_bet_types
        });
        break;
      }
    }
  }

  render() {
    const {
      league, league_divisions, bet_types, selected_bet_types
    } = this.state;
    return (
      <Page
        fullWidth
        breadcrumbs={[{ content: 'Leagues', onAction: this.handleGoBack }]}
        title={league.name}
      >
        <Card sectioned title="League divisions">
          <Layout>
            <Layout.Section oneHalf>
              <div style={{ height: '400px' }}>
                <SortableTree
                  treeData={league_divisions}
                  onChange={(league_divisions) => { this.setState({ league_divisions }); }}
                />
              </div>
            </Layout.Section>
          </Layout>
        </Card>
        <Card sectioned title="Bet types">
          <ChoiceList
            allowMultiple
            choices={bet_types}
            selected={selected_bet_types}
            onChange={this.handleBetTypesChange}
          />
        </Card>
      </Page>
    );
  }
}

export default Detail;
