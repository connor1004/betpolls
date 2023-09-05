/* eslint-disable no-shadow */
import React, { Component } from 'react';
// import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import {
  Page, Card
} from '@shopify/polaris';

import Api from '../../../apis/app';
import Action from './Action';

class LeaderBoard extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isCalculating: false
    };

    this.handleCalculateTotal = this.handleCalculateTotal.bind(this);
  }

  async handleCalculateTotal() {
    const data = await Api.put('admin/bets/leaderboard/calculate-total');
    const {
      response
    } = data;

    switch (response.status) {
      case 200:
        break;
      default:
        break;
    }
  }

  render() {
    return (
      <Page
        title="Leader board"
        fullWidth
      >
        <Card sectioned>
          <Action
            onCalculateTotal={this.handleCalculateTotal}
          />
        </Card>
      </Page>
    );
  }
}


const mapStateToProps = state => ({
  auth: state.common.auth
});

const mapDispatchToProps = () /* dispatch */ => ({
  // logout: bindActionCreators(logout, dispatch)
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(LeaderBoard));
